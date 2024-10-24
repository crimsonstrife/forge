<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the icon definitions from the JSON file
        $jsonFilePath = database_path('data/icons.json');
        $iconsData = $this->loadIconsFromJson($jsonFilePath);

        // Create Icons dynamically
        $this->createIcons($iconsData);
    }

    /**
     * Load icons from a JSON file.
     *
     * @param string $filePath
     * @return array
     */
    private function loadIconsFromJson(string $filePath): array
    {
        if (!File::exists($filePath)) {
            $this->command->error("Icons JSON file not found: {$filePath}");
            return [];
        }

        $jsonContent = File::get($filePath);
        return json_decode($jsonContent, true);
    }

    /**
     * Create Icons dynamically based on the provided icon data.
     *
     * @param array $iconsData
     */
    private function createIcons(array $iconsData): void
    {
        // Get each icon set from the JSON data
        foreach ($iconsData['icon-sets'] as $iconSet) {
            $this->command->info("Icon set: {$iconSet['name']}");

            // Get each icon from the set
            foreach ($iconSet['icons'] as $iconData) {
                // Find the SVG file path from the directory based on type, style, and name
                $svgFilePath = $this->findSvgFilePath($iconData['type'], $iconData['style'], $iconData['name']);

                // Create or update the Icon in the database
                $icon = Icon::updateOrCreate(
                    ['name' => $iconData['name']], // Find by icon name
                    [
                        'type' => $iconData['type'],
                        'style' => $iconData['style'] ?? 'regular',
                        'svg_code' => $iconData['svg_code'] ?? null,
                        'svg_file_path' => "icons/builtin/{$iconData['type']}/{$iconData['style']}/{$iconData['name']}.svg", // Store public path
                        'is_builtin' => true,
                    ]
                );

                // Save the icon details
                $icon->save();

                // Display the icon details, if created or updated - if the path did not exist, warn the user
                if ($icon->wasRecentlyCreated) {
                    $this->command->info("Icon created: {$icon->name}");
                    // Notify the user if a file path was not found
                    if ($svgFilePath === null) {
                        $this->command->warn("Icon file path not found: {$icon->name}");
                    }
                } elseif ($icon->wasChanged()) {
                    $this->command->info("Icon updated: {$icon->name}");
                    // Notify the user if a file path was not found
                    if ($svgFilePath === null) {
                        $this->command->warn("Icon file path not found: {$icon->name}");
                    }
                } else {
                    $this->command->error("Icon not created or updated: {$icon->name}");
                }

                // Clear the icon variable
                unset($icon);
            }
        }

        $this->command->info('Icons seeded successfully.');
    }

    /**
     * Find the SVG file path from the built-in icons directory based on type, style, and name.
     *
     * @param string $type
     * @param string $style
     * @param string $name
     * @return string|null
     */
    private function findSvgFilePath(string $type, string $style, string $name): ?string
    {
        // Find the Icon in the database using the type/style/name, determine if it is built-in
        $icon = Icon::where('type', $type)
            ->where('style', $style)
            ->where('name', $name)
            ->first();

        // Is the icon built-in?
        $isBuiltIn = $icon && $icon->isBuiltIn();

        // If the icon is built-in, build the file path using the resource directory
        if ($isBuiltIn) {
            $filePath = public_path("icons/builtin/{$type}/{$style}/{$name}.svg");
            return File::exists($filePath) ? $filePath : null;
        } elseif (!$isBuiltIn) {
            $filePath = public_path("storage/icons/{$type}/{$style}/{$name}.svg");
            return File::exists($filePath) ? $filePath : null;
        }

        return null;
    }
}
