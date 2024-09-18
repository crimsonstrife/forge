<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Utilities\DynamicModelUtility as ModelUtility;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    private $crudActions = ['create', 'read', 'update', 'delete'];
    private $specialPermissions = [
        'access-filament',
        'access-jetstream',
        'access-horizon',
        'access-telescope',
        'manage-settings',
        'view-analytics',
        'is-admin',
        'is-super-admin'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create specialty permissions
        $this->createSpecialPermissions();

        // Create permissions dynamically based on models
        $this->generatePermissions();

        // Re-cache permissions
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Create specialty permissions.
     */
    private function createSpecialPermissions(): void
    {
        if (empty($this->specialPermissions)) {
            $this->command->warn('No special permissions found to create.');
            return;
        }

        foreach ($this->specialPermissions as $permission) {
            $createdPermission = Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);

            if ($createdPermission->wasRecentlyCreated) {
                $this->command->info("Special permission created: {$permission}");
            } else {
                $this->command->comment("Special permission already exists: {$permission}, skipping");
            }
        }
    }

    /**
     * Generate permissions for the application based on models.
     *
     * @return bool True if the permissions were generated successfully, false otherwise.
     */
    private function generatePermissions(): bool
    {
        // Create CRUD permissions for each type of object, e.g. 'users', 'posts', 'comments'
        try {
            // Get all models with the IsPermissable trait
            $models = ModelUtility::getModelsByTrait('App\Traits\IsPermissable');

            // If no models are found, output a warning and return false
            if (empty($models)) {
                $this->command->warn('No models found with the IsPermissable trait.');
                return false;
            }

            // Count models and output for logging
            $this->command->info('Found ' . count($models) . ' models with the IsPermissable trait');

            // Loop through the models and add them to the objects array
            foreach ($models as $model) {
                // Format the model name for the permission
                $modelName = ModelUtility::getNameForPermission($model);
                $modelName = strtolower(str_replace(' ', '-', trim($modelName)));  // Normalize the model name

                // Create CRUD permissions for the model
                $this->createCrudPermissions($modelName);
            }

            return true;
        } catch (\Exception $e) {
            // Log the exception for debugging
            $this->command->error('Error while generating permissions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a permission for each CRUD operation.
     *
     * @param string $object
     * @return void
     * @example $this->createCrudPermissions('user');
     */
    private function createCrudPermissions(string $object): void
    {
        // Loop through each CRUD action and create a permission for it
        foreach ($this->crudActions as $action) {
            $permissionName = "{$action}-{$object}";

            // Use firstOrCreate to ensure permissions are only created if they don't exist
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);

            // Log whether the permission was created or already existed
            if ($permission->wasRecentlyCreated) {
                $this->command->info("Permission created: {$permissionName}");
            } else {
                $this->command->comment("Permission already exists: {$permissionName}, skipping");
            }
        }
    }
}
