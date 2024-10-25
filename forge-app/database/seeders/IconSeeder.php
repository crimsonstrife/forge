<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Seeder;
use BladeUI\Icons\Exceptions\SvgNotFound;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use App\Services\SvgSanitizerService;
use Illuminate\Support\Facades\Log;

use function storage_path;

class IconSeeder extends Seeder
{
    public $storagePath;

    private $styleEnum = ['solid', 'regular', 'light', 'duotone', 'brand', 'outline', 'custom', null];

    private $filesystem;

    private $svgSanitizer;

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
        foreach ($iconSets as $iconSetName => $iconSet) {

            // Get the icon set class
            $iconSetClass = $iconSet['class'];

            // Get the icon set prefix
            $iconSetPrefix = $iconSet['prefix'];

            // Get the icon set path
            $iconSetPath = $iconSet['path'];

            // Get the icons from the set path
            $icons = $this->getIcons($iconSetPath);

            // Get the icons in chunks to avoid memory overload/segfault
            foreach (array_chunk($icons, 100) as $iconChunk) {
                // For each icon in the set, create an icon in the database
                foreach ($iconChunk as $icon) {
                    // Get the icon name
                    $iconName = $icon['name'];

                    // Get the icon path
                    $iconPath = $iconSetPath . '/' . $iconName . '.svg';

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

                // Free memory after each chunk
                gc_collect_cycles();
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
        $iconType = $iconTypeAndStyle[0] ?? 'custom';

        // Get the icon style
        $iconStyle = $iconTypeAndStyle[1] ?? null;

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
        $return = array();

        // Absolute path to the icon set (remove 'public/' from the path)
        $absolutePath = Str::replaceFirst('public/', '', $iconSetPath);

        // Get the icons (svg files) from the set path
        $icons = $this->filesystem->files(public_path($absolutePath));

        // Make sure the icons are not empty, and only contain svg files
        if (empty($icons)) {
            // Log an error if no icons are found
            $this->command->error('No icons found in the set path: ' . $iconSetPath);
        } else {
            // Chunk the icons to avoid memory overload/segfault
            foreach (array_chunk($icons, 100) as $iconChunk) {
                // Loop through the icons
                foreach ($iconChunk as $icon) {
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

                            // Append the icon to the return array
                            $return[] = [
                                'name' => $this->filesystem->name($icon),
                                'path' => $icon,
                                'svg' => $sanitizedSvg,
                            ];
                        } else {
                            // Log an error if the SVG file is not valid
                            $this->command->error('The SVG file is not valid or is unsafe: ' . $icon);
                        }
                    } else {
                        // Log a warning if the file is not an SVG file
                        $this->command->warn('The file is not an SVG file: ' . $icon);
                    }
                }

                // Free memory after each chunk
                gc_collect_cycles();
            }

            // Return the icons
            return $return;
        }

        // Log an error if the icons are empty
        $this->command->error('No icons found in the set path: ' . $iconSetPath);
        Log::error('No icons found in the set path: ' . $iconSetPath);

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
                'svg_code' => $iconSvg,
                'svg_file_path' => $iconPath,
                'is_builtin' => true,
            ]);
        } catch (\Exception $e) {
            // Log an error if the icon could not be created
            $this->command->error('The icon could not be created: ' . $iconName . ' / ' . $iconType . ' / ' . $iconStyle . ' Error: ' . $e->getMessage());
            // Log an error if the icon could not be created
            Log::error('The icon could not be created: ' . $iconName . ' / ' . $iconType . ' / ' . $iconStyle . ' Error: ' . $e->getMessage());
        }
    }
}
