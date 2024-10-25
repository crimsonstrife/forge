<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;
use BladeUI\Icons\Factory;
use BladeUI\Icons\Exceptions\SvgNotFound;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use App\Services\SvgSanitizerService;
use Filament\Forms\Components\KeyValue;
use Illuminate\Support\Facades\Log;

use function storage_path;

class IconSeeder extends Seeder
{
    public $storagePath;

    private $styleEnum = ['solid', 'regular', 'light', 'duotone', 'brand', 'outline'];

    private $filesystem;

    private $disks;

    public function __construct()
    {
        // Initialize the SVG sanitizer service
        $this->svgSanitizer = new SvgSanitizerService();
        // Initialize the filesystem
        $this->filesystem = new Filesystem();
        // Initialize the storage path
        $this->storagePath = storage_path('app/public/icons');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the icon set configuration file from blade-icons
        $iconSets = config('blade-icons.sets');

        // Check if the icon sets are empty
        if (empty($iconSets)) {
            // Log an error if the icon sets are empty
            $this->command->error('No icon sets found in the configuration file.');
        }

        // Create Icons dynamically
        $this->createIcons($iconSets);
    }

    /**
     * Create Icons dynamically based on the provided icon sets.
     * This ensures that the initial icons are available in the database based on what BladeUI Icons provides.
     *
     * @param $iconSets
     */
    private function createIcons($iconSets): void
    {
        // Loop through the icon sets
        foreach ($iconSets as $iconSet) {
            // Get the icon set name - this is the key name the loop is currently on in the icon sets array
            $iconSetName = key($iconSets);

            // Debugging: Log the icon set name
            $this->command->info('Icon Set Name: ' . $iconSetName);

            // Get the icon set class
            $iconSetClass = $iconSet['class'];

            // Debugging: Log the icon set class
            $this->command->info('Icon Set Class: ' . $iconSetClass);

            // Get the icon set prefix
            $iconSetPrefix = $iconSet['prefix'];

            // Debugging: Log the icon set prefix
            $this->command->info('Icon Set Prefix: ' . $iconSetPrefix);

            // Get the icon set path
            $iconSetPath = $iconSet['path'];

            // Debugging: Log the icon set path
            $this->command->info('Icon Set Path: ' . $iconSetPath);

            // Get the icons from the set path
            $icons = $this->getIcons($iconSetPath);

            // For each icon in the set, create an icon in the database
            foreach ($icons as $icon) {
                // Get the icon name
                $iconName = $icon['name'];

                // Get the icon path
                $iconPath = $icon['path'];

                // Get the icon svg code
                $iconSvg = $icon['svg'];

                // Get the icon type and style
                $iconTypeAndStyle = $this->getIconTypeAndStyle($iconSetName);

                // Get the icon type
                $iconType = $iconTypeAndStyle['type'];

                // Get the icon style
                $iconStyle = $iconTypeAndStyle['style'];

                // Create the icon in the database
                $this->createIcon($iconName, $iconType, $iconStyle, $iconSetPrefix, $iconSvg, $iconPath, $iconSetClass, $iconSetName);
            }
        }
    }

    /**
     * Get the type and style of the icon from the set name
     *
     * @param string $iconSetName
     */
    private function getIconTypeAndStyle($iconSetName)
    {
        // Initialize the return array
        $return = [];

        // Get the icon type and style from the icon set name
        $iconTypeAndStyle = explode('-', $iconSetName);

        // Get the icon type
        $iconType = $iconTypeAndStyle[0];

        // Get the icon style
        $iconStyle = $iconTypeAndStyle[1];

        // Check if the icon style is valid
        if (in_array($iconStyle, $this->styleEnum)) {
            // Add the icon type and style to the return array
            $return['type'] = $iconType;
            $return['style'] = $iconStyle;
        } else {
            // Log a warning if the icon style is invalid, and return null
            Log::warning('The icon style is invalid: ' . $iconStyle);

            // Add the icon type and style to the return array
            $return['type'] = $iconType;
            $return['style'] = null;
        }

        // Return the icon type and style
        return $return;
    }

    /**
     * Get the icons present in the set path
     *
     * @param string $iconSetPath
     * @return array $icons
     *
     * @throws FileNotFoundException
     * @throws SvgNotFound
     */
    private function getIcons($iconSetPath)
    {
        // Initialize the icons array
        $icons = [];

        // Initialize the return array
        $return = [];

        // Absolute path to the icon set (remove 'public/' from the path)
        $absolutePath = Str::replaceFirst('public/', '', $iconSetPath);

        // Get the icons (svg files) from the set path
        $icons = $this->filesystem->files(public_path($absolutePath), true);

        // Make sure the icons are not empty, and only contain svg files
        if (empty($icons)) {
            // Log an error if no icons are found
            $this->command->error('No icons found in the set path: ' . $iconSetPath);
            throw new FileNotFoundException('No icons found in the set path: ' . $iconSetPath);
        } else {
            // Loop through the icons
            foreach ($icons as $icon) {
                // Get only the files with the .svg extension
                if ($this->filesystem->extension($icon) === 'svg') {
                    // Get the contents of the svg file for validation/sanitization
                    $svgCode = $this->filesystem->get($icon);

                    // Validate the SVG code
                    $isValid = $this->svgSanitizer->validate($svgCode);

                    // if the SVG is valid, add it to the return array, and sanitize the SVG code
                    if ($isValid) {
                        // Sanitize the SVG code
                        $sanitizedSvg = $this->svgSanitizer->sanitize($svgCode);

                        // Add the icon to the return array
                        $return[] = [
                            'name' => $this->filesystem->name($icon),
                            'path' => $icon,
                            'svg' => $sanitizedSvg,
                        ];
                    } else {
                        // Log an error if the SVG file is not valid
                        $this->command->error('The SVG file is not valid or is unsafe: ' . $icon);
                        throw new SvgNotFound('The SVG file is not valid or is unsafe: ' . $icon);
                    }

                    // Log an error if the SVG file is not found
                    $this->command->error('The SVG file is not found: ' . $icon);
                    Log::error('The SVG file is not found: ' . $icon);
                }

                // Return the icons
                return $return;
            }

            // Log an error if the icons are empty
            $this->command->error('No icons found in the set path: ' . $iconSetPath);
            Log::error('No icons found in the set path: ' . $iconSetPath);
        }

        // Return an empty array if no icons are found
        return [];
    }

    /**
     * Create an icon in the database
     *
     * @param string $iconName
     * @param string $iconType
     * @param string $iconStyle
     * @param string $iconPrefix
     * @param string $iconSvg
     * @param string $iconPath
     * @param string $iconSetClass
     * @param string $iconSetName
     *
     * @return void
     *
     * @throws \Exception
     */
    private function createIcon($iconName, $iconType, $iconStyle, $iconPrefix, $iconSvg, $iconPath, $iconSetClass, $iconSetName): void
    {
        // Absolute path to the icon set (remove 'public/' from the path)
        $absolutePath = Str::replaceFirst('public/', '', $iconPath);

        // Try to create the icon in the database if it does not already exist
        try {
            // Create the icon in the database
            Icon::firstOrCreate([
                'name' => $iconName,
                'type' => $iconType,
                'style' => $iconStyle,
                'prefix' => $iconPrefix,
                'set' => $iconSetName,
                'class' => $iconSetClass,
                'svg' => $iconSvg,
                'path' => $absolutePath,
                'is_builtin' => true,
            ]);
        } catch (\Exception $e) {
            // Log an error if the icon could not be created
            $this->command->error('The icon could not be created: ' . $iconName . ' / ' . $iconType . ' / ' . $iconStyle);
            // Log an error if the icon could not be created
            Log::error('The icon could not be created: ' . $iconName . ' / ' . $iconType . ' / ' . $iconStyle);
        }
    }
}
