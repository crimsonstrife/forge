<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use App\Services\SvgSanitizerService;
use Clockwork\Request\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class Icon extends Model
{
    use HasFactory;
    use IsPermissable;

    public $storagePath;

    private $filesystem;

    protected $fillable = [
        'name',
        'type',
        'style',
        'prefix',
        'set',
        'class',
        'svg_code',
        'svg_file_path',
        'is_builtin',
    ];

    /**
     * Constructor
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Initialize the filesystem
        $this->filesystem = new Filesystem();
        // Initialize the storage path
        $this->storagePath = storage_path('app/public/icons');
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Clear the cache when Icons are saved or deleted
        static::saved(function () {
            cache()->forget('icons.all');
        });

        // Clear the cache when Icons are saved or deleted
        static::deleted(function () {
            cache()->forget('icons.all');
        });

        static::saving(function ($model) {
            $query = static::where('name', $model->name)
                ->where('type', $model->type)
                ->where('style', $model->style);

            if ($model->exists) {
                $query->where('id', '!=', $model->id);
            }

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'name' => 'The name has already been taken for this type and style.'
                ]);
            }
        });
    }

    /**
     * Determine if the icon is a custom uploaded SVG.
     */
    public function isCustom(): bool
    {
        // Custom icons are those that have an SVG file path or inline SVG code, and are not Heroicons or Font Awesome icons.
        // If the result would be null, it will be cast to false always.
        if ($this->svg_file_path || $this->svg_code) {
            return !$this->isHeroicon() && !$this->isFontAwesome() && !$this->isOcticon();
        } else {
            return false;
        }
    }

    /**
     * Determine if the icon is a Font Awesome icon.
     */
    public function isFontAwesome(): bool
    {
        return $this->type === 'fontawesome';
    }

    /**
     * Determine if the icon is a Heroicon.
     */
    public function isHeroicon(): bool
    {
        return $this->type === 'heroicon';
    }

    /**
     * Determin if the icon is a Octicon.
     */
    public function isOcticon(): bool
    {
        return $this->type === 'octicons';
    }

    /**
     * Get if the icon is built-in or user-uploaded.
     *
     * @return bool
     */
    public function isBuiltIn(): bool
    {
        //if the icon is built-in, it will have a value of true, otherwise false. If the value is null, it will be cast to false.
        $isBuiltIn = $this->is_builtin;

        if ($isBuiltIn === null) {
            return false;
        }

        return $isBuiltIn;
    }

    /**
     * Load all icons from the {type}/{style} sub-directories.
     */
    public function loadAllIcons()
    {
        /* // Load built-in icons from resources/icons/builtin/{type}/{style}
        $builtinIcons = $this->loadIconsFromDirectory(resource_path('icons/builtin'));

        // Load user-uploaded icons from public/icons/{type}/{style}
        $userIcons = $this->loadIconsFromDirectory(storage_path('app/public/icons'));

        return $builtinIcons->merge($userIcons); */

        // Load all icons from the database, cache the results for 1 hour (60 minutes) before refreshing.
        return cache()->remember('icons.all', 60 * 60, function () {
            return self::all();
        });
    }

    /**
     * Load all icon types from the database.
     * Only return unique types, no duplicates.
     */
    public function loadTypes()
    {
        return self::select('type')
            ->distinct()
            ->pluck('type');
    }

    /**
     * Load all icon styles from the database.
     * Only return unique styles, no duplicates.
     */
    public function loadStyles()
    {
        return self::select('style')
            ->distinct()
            ->pluck('style');
    }

    /**
     * Get the style class for the icon.
     *
     * @return string
     */
    public function getStyleClass(Icon $icon): string
    {
        // Create the style class based on the icon style, type, and name. The styles for Heroicons and Font Awesome icons are different.
        if ($icon->isHeroicon()) {
            // Heroicons style class starts with "heroicon-" followed by a prefix based on the icon style, e.g., "o" for "outline", "s" for "solid", etc.
            return 'heroicon-' . substr($icon->style, 0, 1) . '-' . $icon->name;
        }

        if ($icon->isFontAwesome()) {
            // Font Awesome style class starts with "fa" followed by the icon style, e.g., "fa-brands", "fa-solid", etc., and then "fa-" and the icon name.
            return 'fa-' . $icon->style . '-' . $icon->name;
        }

        if ($icon->isOcticon()) {
            // Octicons style class starts with "octicon" followed by the icon name.
            return 'octicon-' . $icon->style . '-' . $icon->name;
        }

        // Custom icons will use a custom class name based on the type, style, and name.
        return 'icon-' . $icon->type . '-' . $icon->style . '-' . $icon->name;
    }

    /**
     * Ensure the file name is unique within the given directory.
     *
     * @param string $directory
     * @param string $fileName
     * @param string $extension
     * @return string
     */
    public static function ensureUniqueFileName(string $directory, string $fileName, string $extension)
    {
        $counter = 1; // Start the counter at 1
        $baseFileName = \pathinfo($fileName, \PATHINFO_BASENAME); // Get the base file name without the extension

        // Loop until a unique file name is found
        while (Storage::exists("{$directory}/{$fileName}")) {
            $fileName = "{$baseFileName}-{$counter}"; // Append the counter to the base file name
            $counter++; // Increment the counter
        }

        // Return the unique file name with the extension re-appended
        return "{$fileName}.{$extension}";
    }

    /**
     * Before saving the icon
     * This method is used to ensure the icon is setup correctly before saving it.
     *
     * @param $record
     */
    public function beforeSave($record)
    {
        // Get the authenticated user and check if they have the relevant permission.
        $user = Auth::user();
        // Create the permission name dynamically based on the model name
        $permission = 'create-icon';

        // Only proceed if the user has the necessary permission
        if (!$user->can($permission)) {
            Log::channel('single')->warning("User {$user->id} does not have permission to create icons.");
            throw new \Exception('You do not have permission to create icons.');
        }

        if ($record->is_builtin) {
            throw new \Exception('Built-in icons cannot be modified.');
        }

        if (!$record->name) {
            throw new \Exception('The icon name is required.');
        }

        if (!$record->type) {
            throw new \Exception('The icon type is required.');
        }

        if (!$record->style) {
            throw new \Exception('The icon style is required.');
        }

        if (!$record->set) {
            throw new \Exception('The icon set is required.');
        }

        // Ensure the prefix and class are set and correct, else set them based on the type and style
        if (!$record->prefix) {
            $record->prefix = $this->makeIconPrefix($record->type, $record->style);
        } else {
            // Compare the prefix with the generated prefix based on the type and style, and update it if they don't match
            $generatedPrefix = $this->makeIconPrefix($record->type, $record->style);
            if ($record->prefix !== $generatedPrefix) {
                $record->prefix = $generatedPrefix;
            }
        }

        if (!$record->class) {
            $record->class = $this->makeIconClass($record->type, $record->style);
        } else {
            // Compare the class with the generated class based on the type and style, and update it if they don't match
            $generatedClass = $this->makeIconClass($record->type, $record->style);
            if ($record->class !== $generatedClass) {
                $record->class = $generatedClass;
            }
        }
    }

    /**
     * Save the icon.
     * This method is used to save the uploaded icon file to the storage directory and save the icon details in the database.
     *
     * @param array $options
     * @return void
     *
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        $directory = $this->storagePath;
        $filename = null;
        $temporaryFile = null;

        // Ensure the icon is setup correctly before saving it
        $this->beforeSave($this);

        // If a file was uploaded, set the temporary file path
        if ($this->svg_file_path) {
            $temporaryFile = $this->svg_file_path;
        }

        // If the icon is built-in, set the directory based on the type and style
        if ($this->is_builtin) {
            // TODO: Implement the built-in icons save logic - though it's not necessary as built-in icons are not user-uploaded
            // Append the builtin directory to the storage path
            $directory = $directory . '/builtin/';
        } else {
            // If the icon is not built-in, append custom to the directory
            $directory = $directory . '/custom/';
        }
        // If there is a file path, or custom svg code, set the directory based on the type and style, and ensure it exists
        if ($this->svg_file_path || $this->svg_code) {
            // Type should be custom in this case, as it's a user-uploaded icon
            $this->type = 'custom';
            // If the style is empty, set it to 'regular' as a default
            $this->style = $this->style ?: 'regular';
            // Establish the directory for the icon
            $directory = $directory . "{$this->type}/{$this->style}";
            // Ensure the directory exists
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
        }

        // If the directory is set, proceed to save the icon file
        if ($directory) {
            // Set the file name based on the provided name of the icon, making it URL-friendly and unique
            $filename = Str::slug(pathinfo($this->name, \PATHINFO_BASENAME)); // Example: "icon-name.svg"
            // Set the file path based on the directory and file name
            $filePath = "{$directory}/{$filename}";

            // Confirm if the file path is set and not empty, then check that the name is unique
            if ($this->svg_file_path || $this->svg_code) {
                // Ensure the file name is unique within the directory
                $filename = self::ensureUniqueFileName($directory, $filename, 'svg');
                // Update the file path based on the unique file name
                $filePath = "{$directory}/{$filename}";
            }

            // Was the icon uploaded as a file?
            if ($this->svg_file_path) {
                // Move the uploaded file to the storage directory and rename it
                Storage::disk('local')->move($this->svg_file_path, $filePath);
            } elseif ($this->svg_code) {
                // If the icon was uploaded as SVG code, save the code to the file
                // Sanitize the SVG code before saving
                $sanitizer = app(SvgSanitizerService::class);

                // Sanitize the SVG code before saving
                $sanitized = $sanitizer->sanitize($this->svg_code);

                if ($sanitized === null) {
                    // Return an error message if the SVG is invalid
                    throw new \Exception('Invalid SVG code.');
                }

                // Save the sanitized SVG code to the file
                Storage::disk('local')->put($filePath, $sanitized);
            }

            // Save the file path in the database, without any preceding slashes
            $this->svg_file_path = ltrim($filePath, '/');

            // Remove the temporary file from the icons directory
            if ($temporaryFile) {
                File::delete($temporaryFile);
            }
        }

        // Save the icon details in the database
        parent::save($options);
    }

    /**
     * Save uploaded file to the appropriate directory.
     *
     * @param \Illuminate\Http\UploadedFile|string $uploadedFile - The uploaded file or SVG code.
     * @param string $type - The icon type, e.g., "fontawesome", "heroicon", "octicons", etc.
     * @param string $style - The icon style, e.g., "brands", "solid", "regular", etc.
     * @param string $name - The name of the icon.
     * @return string|null
     * @throws \Exception
     */
    public static function saveIconFile(\Illuminate\Http\UploadedFile|string $uploadedFile, string $type, string $style, string $name)
    {
        // Initialize the SVG Sanitizer service
        $sanitizer = app(SvgSanitizerService::class);

        // Set the directory based on the type and style
        $directory = "public/icons/custom/{$type}/{$style}";

        // Is the uploaded file an instance of an uploaded file or a string of SVG code?
        if (is_string($uploadedFile)) {
            // If the uploaded file is not an instance of an uploaded file, then it should be a string of svg code
            $svgCode = $uploadedFile;

            // Sanitize the SVG code before saving
            $sanitized = $sanitizer->sanitize($svgCode);

            if ($sanitized === null) {
                // Return an error message if the SVG is invalid
                throw new \Exception('Invalid SVG code.');
            }

            // Make an SVG file based on the SVG code, with a unique file name based on the name of the icon
            $uniqueFileName = self::ensureUniqueFileName($directory, Str::slug($name), 'svg'); // Set the file name

            // Save the sanitized SVG code to the file
            try {
                Storage::disk('local')->put("{$directory}/{$uniqueFileName}", $sanitized);
            } catch (\Exception $e) {
                // Return an error message if the SVG code could not be saved
                throw new \Exception('Could not save the SVG code.');
            }

            return "{$directory}/{$uniqueFileName}";

        } elseif ($uploadedFile instanceof \Illuminate\Http\UploadedFile) {
            $fileName = pathinfo($uploadedFile->getClientOriginalName(), \PATHINFO_BASENAME);
            $extension = $uploadedFile->getClientOriginalExtension();

            // Ensure the file name is unique within the directory
            $uniqueFileName = self::ensureUniqueFileName($directory, $fileName, $extension);

            // Validate the file as an SVG file, check for errors, etc.
            $uploadedFile->validate([
                'file' => 'required|file|mimes:svg',
            ]);

            // Read the file contents and sanitize the SVG code
            $svgCode = File::get($uploadedFile->getRealPath());

            // Sanitize the SVG code before saving
            $sanitized = $sanitizer->sanitize($svgCode);

            if ($sanitized === null) {
                // Return an error message if the SVG is invalid
                throw new \Exception('Invalid SVG code.');
            }

            return "{$directory}/{$uniqueFileName}";
        }

        return null;
    }

    /**
     * Delete the icon file from storage.
     */
    public function deleteIconFile()
    {
        if ($this->svg_file_path) {
            Storage::disk('public')->delete($this->svg_file_path);
        }
    }

    /**
     * Get the full URL for the icon's SVG file path.
     */
    public function getSvgUrlAttribute()
    {
        $path = $this->svg_file_path;

        if ($this->is_builtin) {
            // Check if the svg_file_path is valid (not null and not empty)
            if (!empty($path) && file_exists(public_path($path))) {
                // Pre-append the public path to the file path
                $fullPath = 'public/' . $path;

                // Check if the file path exists, if so return the URL
                if (File::exists($path)) {
                    return asset($fullPath);
                } else {
                    // If the file path does not exist, return null and log a warning
                    logger()->warning("Built-in icon {$this->type}/{$this->style}/{$this->name} has an invalid file path. Path: {$path}");
                    return null;
                }
            } else {
                // If the icon has no valid file path, return an empty string and log a warning
                logger()->warning("Built-in icon {$this->type}/{$this->style}/{$this->name} has no valid file path.");
                return '';
            }
        } else {
            // If it's a user-uploaded icon, use the storage path
            return !empty($path) ? Storage::url($path) : '';
        }
    }

    /**
     * Get the SVG code for the icon.
     */
    public function getSvgCodeAttribute()
    {
        if ($this->is_builtin) {
            // Check if the svg_code is valid (not null and not empty)
            if (!empty($this->svg_code)) {
                // Instantiate the SVG sanitizer service
                $sanitizer = app(SvgSanitizerService::class);

                // Sanitize the SVG code before returning it
                $sanitizedSvg = $sanitizer->sanitize($this->svg_code);

                // Return the sanitized SVG code if it's not empty
                if (!empty($sanitizedSvg)) {
                    return $sanitizedSvg;
                } else {
                    // If the sanitized SVG code is empty, return an empty string and log a warning
                    logger()->warning("Built-in icon {$this->type}/{$this->style}/{$this->name} has invalid SVG code.");
                    return '';
                }
            } else {
                // If the icon has no valid SVG code, return an empty string and log a warning
                logger()->warning("Built-in icon {$this->type}/{$this->style}/{$this->name} has no SVG code.");
                return '';
            }
        } else {
            // If the svg_code is empty, return empty without sanitizing
            if (empty($this->svg_code)) {
                // If the icon has no SVG code, return an empty string and log a warning
                logger()->warning("User-uploaded icon {$this->type}/{$this->style}/{$this->name} has no SVG code.");
                return '';
            }

            // If it's a user-uploaded icon, sanitize the SVG code before returning it
            $sanitizer = app(SvgSanitizerService::class);

            // Sanitize the SVG code before returning it
            return $sanitizer->sanitize($this->svg_code);
        }
    }

    /**
     * Get the icon SVG
     * Returns the SVG file path, or the SVG code - else a null value. An alias for the getSvgUrlAttribute and getSvgCodeAttribute methods.
     *
     * @param Icon|int $icon - The icon object or icon id.
     * @return string|null
     *
     * @see getSvgUrlAttribute
     * @see getSvgCodeAttribute
     */
    public function getSvg(Icon|int $icon): string|null
    {
        // If the icon is an integer, assume it's an icon id and attempt to load the icon from the database
        if (is_int($icon)) {
            $icon = self::findOrFail($icon);
        }

        // Check if the icon is an instance of the Icon model
        if (!$icon instanceof Icon) {
            // Log a warning if the icon is not an instance of the Icon model
            logger()->warning('The icon is not an instance of the Icon model.');
            // Return a null value if the icon is not an instance of the Icon model
            return null;
        }

        // Check if the icon is built-in
        if ($icon->is_builtin) {
            // Check if the icon has an SVG file path, if so prioritize the SVG file path, else return the SVG code
            $svgFile = $icon->getSvgUrlAttribute();
            if (!empty($svgFile)) {
                return $svgFile;
            } else {
                // Get the SVG code if the SVG file path is empty
                $svgCode = $icon->getSvgCodeAttribute();
                if (!empty($svgCode)) {
                    return $svgCode;
                } else {
                    // Return a null value if the icon has no SVG file path or SVG code
                    return null;
                }
            }
        } else {
            // If it's a user-uploaded icon, use the storage path if the SVG file path is not empty, else return the SVG code
            $svgFile = $icon->getSvgUrlAttribute();
            if (!empty($svgFile)) {
                return $svgFile;
            } else {
                // Get the SVG code if the SVG file path is empty
                $svgCode = $icon->getSvgCodeAttribute();
                if (!empty($svgCode)) {
                    return $svgCode;
                } else {
                    // Return a null value if the icon has no SVG file path or SVG code
                    return null;
                }
            }
        }
    }

    /**
     * Should the svg file or code be used?
     * Returns a boolean value based on if the icon is built-in or user-uploaded, and if the icon has an SVG file path or SVG code.
     * It will prioritize the SVG file path over the SVG code.
     *
     * @param Icon|int $icon - The icon object or icon id.
     *
     * @return bool
     */
    public function isFile(Icon|int $icon): bool
    {
        // If the icon is an integer, assume it's an icon id and attempt to load the icon from the database
        if (is_int($icon)) {
            $icon = self::findOrFail($icon);
        }

        // Check if the icon is an instance of the Icon model
        if (!$icon instanceof Icon) {
            // Log a warning if the icon is not an instance of the Icon model
            logger()->warning('The icon is not an instance of the Icon model.');
            // Return false if the icon is not an instance of the Icon model
            return false;
        }

        // Check if the icon is built-in
        if ($icon->is_builtin) {
            // Check if the icon has an SVG file path, if so return true, else return false
            return !empty($icon->getSvgUrlAttribute());
        } else {
            // If it's a user-uploaded icon, return true if the SVG file path is not empty, else return false
            return !empty($icon->getSvgUrlAttribute());
        }
    }

    /**
     * Make the icon prefix based on the icon type and style.
     * This method is used to set the icon prefix based on the icon type and style, ensuring the correct prefix is used for the icon.
     *
     * @param string $type - The icon type, e.g., "fontawesome", "heroicon", "octicons", etc.
     * @param string $style - The icon style, e.g., "brands", "solid", "regular", etc.
     *
     * @return string - The icon prefix based on the icon type and style.
     */
    public function makeIconPrefix(string $type, string $style): string
    {
        // Get the config for the blade-icons package
        $bladeIcons = Config::get('blade-icons', []);

        // Get the sets from the blade-icons configuration
        $sets = $bladeIcons['sets'] ?? [];

        // Get the prefixes from the sets, and pair them with the set name/key
        $prefixes = collect($sets)->mapWithKeys(fn ($set, $key) => [$key => $set['prefix']]);

        // The prefixes are stored on the set name, which is a combination of the type and style (e.g., "fontawesome-solid")
        $setName = "{$type}-{$style}";

        // Get the prefix for the set name, or return an empty string if it doesn't exist
        $prefix = $prefixes[$setName] ?? '';

        // Return the prefix for the icon
        return $prefix;
    }

    /**
     * Make the icon class based on the icon type, style
     *
     * @param string $type - The icon type, e.g., "fontawesome", "heroicon", "octicons", etc.
     * @param string $style - The icon style, e.g., "brands", "solid", "regular", etc.
     *
     * @return string - The icon class based on the icon type and style.
     */
    public function makeIconClass(string $type, string $style): string
    {
        // Get the config for the blade-icons package
        $bladeIcons = Config::get('blade-icons', []);

        // Get the sets from the blade-icons configuration
        $sets = $bladeIcons['sets'] ?? [];

        // Get the classes from the sets, and pair them with the set name/key
        $classes = collect($sets)->mapWithKeys(fn ($set, $key) => [$key => $set['class']]);

        // The classes are stored on the set name, which is a combination of the type and style (e.g., "fontawesome-solid")
        $setName = "{$type}-{$style}";

        // Get the class for the set name, or return an empty string if it doesn't exist
        $class = $classes[$setName] ?? '';

        // Return the class for the icon
        return $class;
    }
}
