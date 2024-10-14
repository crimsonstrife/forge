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
                // Create or update the Icon
                $icon = Icon::firstOrCreate(['name' => $iconData['name']], [
                    'type' => $iconData['type'],
                    'style' => $iconData['style'] ?? 'regular',
                    'svg_code' => $iconData['svg_code'] ?? null,
                    'svg_file_path' => $iconData['svg_file_path'] ?? null,
                ]);

                // Output the icon name
                $this->command->info("Icon created: {$icon->type} - {$icon->style} - {$icon->name}");
            }
        }

        $this->command->info('Icons seeded successfully.');
    }
}
