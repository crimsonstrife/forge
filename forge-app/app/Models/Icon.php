<?php

namespace App\Models;

use App\Traits\IsPermissable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Services\SvgSanitizerService;

class Icon extends Model
{
    use HasFactory;
    use IsPermissable;

    protected $fillable = [
        'name',
        'type',
        'style',
        'svg_code',
        'svg_file_path',
        'is_builtin',
    ];

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

        // Load all icons from the database
        return self::all();
    }

    /**
     * Get the style class for the icon.
     *
     * @return string
     */
    public function getStyleClass(): string
    {
        // Create the style class based on the icon style, type, and name. The styles for Heroicons and Font Awesome icons are different.
        if ($this->isHeroicon()) {
            // Heroicons style class starts with "heroicon-" followed by a prefix based on the icon style, e.g., "o" for "outline", "s" for "solid", etc.
            return 'heroicon-' . substr($this->style, 0, 1) . '-' . $this->name;
        }

        if ($this->isFontAwesome()) {
            // Font Awesome style class starts with "fa" followed by the icon style, e.g., "fa-brands", "fa-solid", etc., and then "fa-" and the icon name.
            return 'fa-' . $this->style . ' fa-' . $this->name;
        }

        // Custom icons will use a custom class name based on the type, style, and name.
        return 'icon-' . $this->type . '-' . $this->style . '-' . $this->name;
    }

    /**
     * Helper function to load icons from a base directory with sub-directories {type}/{style}
     *
     * @param string $baseDirectory
     * @return \Illuminate\Support\Collection
     */
    private function loadIconsFromDirectory(string $baseDirectory)
    {
        if (!File::exists($baseDirectory)) {
            return collect();
        }

        $icons = collect();

        // Traverse the {type}/{style} directories and load icons
        foreach (File::directories($baseDirectory) as $typeDir) {
            foreach (File::directories($typeDir) as $styleDir) {
                $type = basename($typeDir);
                $style = basename($styleDir);

                $files = File::files($styleDir);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                        $icons->push([
                            'name' => pathinfo($file, PATHINFO_FILENAME),
                            'path' => str_replace(base_path(), asset(''), $file),
                            'type' => $type,
                            'style' => $style,
                        ]);
                    }
                }
            }
        }

        return $icons;
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
        $counter = 1;
        $originalFileName = $fileName;

        while (Storage::disk('public')->exists("{$directory}/{$fileName}.{$extension}")) {
            $fileName = "{$originalFileName}-{$counter}";
            $counter++;
        }

        return "{$fileName}.{$extension}";
    }

    /**
     * Save uploaded file to the appropriate directory.
     *
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param string $type
     * @param string $style
     * @return string
     */
    public static function saveIconFile($uploadedFile, string $type, string $style)
    {
        $directory = "icons/{$type}/{$style}";
        $fileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $uploadedFile->getClientOriginalExtension();

        // Ensure the file name is unique within the directory
        $uniqueFileName = self::ensureUniqueFileName($directory, $fileName, $extension);

        // Validate the file as an SVG file, check for errors, etc.
        $uploadedFile->validate([
            'file' => 'required|file|mimes:svg',
        ]);

        // Sanitize the SVG code before saving
        $sanitizer = app(SvgSanitizerService::class);

        // Read the file contents and sanitize the SVG code
        $svgCode = File::get($uploadedFile->getRealPath());

        // Sanitize the SVG code before saving
        $sanitized = $sanitizer->sanitize($svgCode);

        if ($sanitized === null) {
            // Return an error message if the SVG is invalid
            return 'Invalid SVG code.';
        }

        // Save the sanitized SVG code to the file
        Storage::disk('public')->put("{$directory}/{$uniqueFileName}", $sanitized);

        return "{$directory}/{$uniqueFileName}";
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
        if ($this->is_builtin) {
            // Check if the svg_file_path is valid (not null and not empty)
            if (!empty($this->svg_file_path)) {
                // Generate the URL using the custom route that serves files from the resources folder
                return route('icon.builtin', [
                    'type' => $this->type,
                    'style' => $this->style,
                    'file' => basename($this->svg_file_path), // Extract the filename from the path
                ]);
            } else {
                // If the icon has no valid file path, return an empty string and log a warning
                logger()->warning("Built-in icon {$this->type}/{$this->style}/{$this->name} has no valid file path.");
                return '';
            }
        } else {
            // If it's a user-uploaded icon, use the storage path
            return !empty($this->svg_file_path) ? Storage::url($this->svg_file_path) : '';
        }
    }
}
