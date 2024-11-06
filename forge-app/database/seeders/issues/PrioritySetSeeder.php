<?php

namespace Database\Seeders\issues;

use App\Models\PrioritySet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Utilities\DynamicModelUtility as ModelUtility;
use JsonException;

/**
 * Class PrioritySetSeeder
 *
 * A Seeder class responsible for populating the PrioritySets table with initial data.
 */
class PrioritySetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws JsonException
     */
    public function run(): void
    {
        // Load the issue priority definitions from the JSON file
        $jsonFilePath = database_path('data/issue_priorities.json');
        $prioritiesData = $this->loadPrioritiesFromJson($jsonFilePath);

        // Create PrioritySets dynamically
        $this->createPrioritySets($prioritiesData);
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
     * Create and populate PrioritySets from the provided priorities' data.
     *
     * @param array $prioritiesData Associative array containing 'priority_sets' and 'issue_priorities' data.
     * @return void
     */
    private function createPrioritySets(array $prioritiesData): void
    {
        // Instance the PrioritySet model
        $prioritySet = new PrioritySet();
        foreach (array_chunk($prioritiesData['priority_sets'], 10) as $setChunk) {
            foreach ($setChunk as $set) {
                $name = $set['name'];
                $description = $set['description']; // Description not currently used.

                // Create the PrioritySet
                $prioritySet->create([
                    'name' => $name,
                    'description' => $description,
                ]);

                // Log the creation of the PrioritySet
                $this->command->info("PrioritySet created: {$name}");
            }

            // Free memory after each chunk
            gc_collect_cycles();
        }

        // Free memory after all sets
        gc_collect_cycles();

        foreach (array_chunk($prioritiesData['priority_sets'], 10) as $setChunk) {
            foreach ($setChunk as $set) {
                $name = $set['name'];

                // Get the PrioritySet by name
                $thisPrioritySet = $prioritySet->where('name', $name)->first();

                // Get the priorities from the dataset that match the current set
                $priorities = array_filter($prioritiesData['issue_priorities'], static function ($priority) use ($name) {
                    return $priority['set'] === strtolower($name);
                });

                // Get the IDs of the priorities that match the current set, and map them to the PrioritySet
                $priorityIds = array_map(static function ($priority) use ($name) {
                    return ModelUtility::getModelId('IssuePriority', $priority['name']);
                }, $priorities);

                // For each priority ID, attach it to the PrioritySet
                foreach ($priorityIds as $priorityId) {
                    $thisPrioritySet->priorities()->attach($priorityId);

                    // Log the attachment of the priority to the PrioritySet
                    $this->command->info("Priority attached to PrioritySet: {$name}");
                }

                // Log the attachment of the priorities to the PrioritySet
                $this->command->info("Priorities attached to PrioritySet: {$name}");
            }

            // Free memory after each chunk
            gc_collect_cycles();
        }

        // Free memory after all sets
        gc_collect_cycles();
    }
}
