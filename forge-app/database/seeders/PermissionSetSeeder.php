<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Utilities\DynamicModelUtility as ModelUtility;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\PermissionSet;

class PermissionSetSeeder extends Seeder
{
    use WithoutModelEvents;

    private $modelsWithoutIsPermissable = ['permission'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create default PermissionSets dynamically
        $this->createDynamicPermissionSets();

        // Re-cache permissions
        app()->make(\App\Models\Auth\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Dynamically create PermissionSets based on models and available permissions.
     */
    private function createDynamicPermissionSets(): void
    {
        // Get all models that use the IsPermissable trait
        $models = ModelUtility::getModelsByTrait('App\Traits\IsPermissable');

        // Add additional models as needed that don't use the IsPermissable trait
        $models = array_merge($models, $this->modelsWithoutIsPermissable);

        // Loop through each model and create a PermissionSet for it
        foreach ($models as $model) {
            // Format the model name for the permission
            $modelName = ModelUtility::getNameForPermission($model);
            $setName = ucfirst($modelName) . ' Management';  // e.g., 'Project Management'
            $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name

            // Generate permission names (CRUD pattern)
            $permissions = [
                "create-{$modelName}",
                "read-{$modelName}",
                "update-{$modelName}",
                "delete-{$modelName}",
                "list-{$modelName}",
                "restore-{$modelName}",
                "force-delete-{$modelName}",
                "export-{$modelName}",
                "import-{$modelName}",
                "reorder-{$modelName}",
            ];

            // Create or update the PermissionSet for this model
            $permissionSet = PermissionSet::firstOrCreate(['name' => $setName, 'guard_name' => 'web']);

            // Get the corresponding permission IDs and check if they exist
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();

            if (count($permissionIds) < count($permissions)) {
                $missingPermissions = array_diff($permissions, Permission::whereIn('name', $permissions)->pluck('name')->toArray());
                $this->command->warn("Missing permissions for {$modelName}: " . implode(', ', $missingPermissions));
            }

            // Sync existing permission IDs with the PermissionSet
            $permissionSet->permissions()->sync($permissionIds);

            // Log the creation or update of the PermissionSet
            $this->command->info("PermissionSet created/updated: {$setName}");
        }
    }
}
