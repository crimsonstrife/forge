<?php

namespace Database\Seeders\issues;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Utilities\DynamicModelUtility as ModelUtility;
use App\Models\Issues\IssuePriority;
use App\Models\Icon;
use JsonException;

/**
 * Class IssuePrioritySeeder
 *
 * A Seeder class responsible for populating the IssuePriorities table with initial data.
 */
class IssuePrioritySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * @throws JsonException
     */
    public function run(): void
    {
        // Load the issue priority definitions from the JSON file
        $jsonFilePath = database_path('data/issue_priorities.json');
        $prioritiesData = $this->loadPrioritiesFromJson($jsonFilePath);

        // Create IssuePriorities dynamically
        $this->createIssuePriorities($prioritiesData);
    }

    /**
     * Load issue priorities from a JSON file.
     *
     * @param string $filePath
     * @return array
     * @throws JsonException
     */
    private function loadPrioritiesFromJson(string $filePath): array
    {
        if (!File::exists($filePath)) {
            $this->command->error("Issue priorities JSON file not found: {$filePath}");
            return [];
        }

        $jsonContent = File::get($filePath);
        return json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Create IssuePriorities dynamically based on the provided priority data.
     *
     * @param array $prioritiesData
     */
    private function createIssuePriorities(array $prioritiesData): void
    {
        foreach (array_chunk($prioritiesData['issue_priorities'], 10) as $priorityChunk) {
            foreach ($priorityChunk as $priorityData) {
                $description = $priorityData['description'];
                $iconName = $priorityData['icon'];
                $colorCode = $priorityData['color'];
                $name = $priorityData['name'];

                // Get the icon ID from the icon name, this should be structured as prefix-name, e.g. fas-exclamation-triangle for Font Awesome Solid exclamation triangle
                // Split the icon name by the first hyphen to get the prefix and the name
                $iconNameParts = explode('-', $iconName, 2);
                $iconPrefix = $iconNameParts[0];
                $iconName = $iconNameParts[1];
                // Find the icon by the prefix and name
                $icon = Icon::where('prefix', $iconPrefix)->where('name', $iconName)->first();

                // Create the IssuePriority
                IssuePriority::create([
                    'name' => $name,
                    'description' => $description ?? null,
                    'color' => $colorCode,
                    'icon' => $icon->id,
                    'is_default' => $priorityData['is_default'] ?? false,
                ]);

                // Log the priority creation
                $this->command->info("Created Issue Priority: {$name}");
            }

            // Free memory after each chunk
            gc_collect_cycles();
        }

        // Free memory after all priorities are created
        gc_collect_cycles();
    }
}
