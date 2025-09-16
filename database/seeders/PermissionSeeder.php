<?php

namespace Database\Seeders;

use App\Utilities\DynamicModelUtility as ModelUtility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /** Actions we want for most resources */
    private array $crud = ['view', 'create', 'update', 'delete'];

    private array $extras = [
        'view_any', 'delete_any', 'restore', 'restore_any',
        'force_delete', 'force_delete_any', 'replicate', 'reorder',
        'export', 'import', 'manage', 'list',
    ];

    /** Map model base name => permission "domain" (plural snake) */
    private array $nameMap = [
        'User'          => 'users',
        'Team'          => 'teams',
        'Project'       => 'projects',
        'Issue'         => 'issues',
        'Comment'       => 'comments',
        'Permission'    => 'permissions',
        'Role'          => 'roles',
        'IssueType'     => 'issue_types',
        'IssueStatus'   => 'issue_statuses',
        'IssuePriority' => 'issue_priorities',
    ];

    /** Feature/flag permissions (mutes ignore these) */
    private array $flags = [
        'is-admin',
        'is-super-admin',
        'is-panel-user',
    ];

    /** Special capability permissions (dot style so mutes can work) */
    private array $specials = [
        'attachments.manage',
        'admin.panel.access',
        'admin.connectors.manage',
        'admin.mappings.manage',
        'projects.transition',
        'issues.transition',
        'filament.access',
        'jetstream.access',
        'horizon.access',
        'telescope.access',
        'settings.manage',
        'settings.view',
        'analytics.view',
    ];

    public function run(): void
    {
        // Clear Spatie cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Use the model from config (UUID-safe)
        $Permission = app(config('permission.models.permission'));

        // 1) Flags (e.g., is-super-admin)
        foreach ($this->flags as $name) {
            $Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // 2) Specials (dot style)
        foreach (array_chunk($this->specials, 20) as $chunk) {
            foreach ($chunk as $name) {
                $Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            }
        }

        // 3) Generate for permissible models
        $models = ModelUtility::getModelsByTrait('App\Traits\IsPermissible');

        foreach ($models as $fqcn) {
            $domain = $this->domainFromModel($fqcn);

            // CRUD
            foreach ($this->crud as $action) {
                $Permission::firstOrCreate(['name' => "{$domain}.{$action}", 'guard_name' => 'web']);
            }

            // Extras
            foreach ($this->extras as $action) {
                $Permission::firstOrCreate(['name' => "{$domain}.{$action}", 'guard_name' => 'web']);
            }
        }

        // Ensure we have CRUD/extras for the permissions resource itself
        foreach (array_merge($this->crud, $this->extras) as $action) {
            $Permission::firstOrCreate(['name' => "permissions.{$action}", 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** Turn FQCN into a plural snake "domain" used in permission names. */
    private function domainFromModel(string $fqcn): string
    {
        $base = class_basename($fqcn);

        if ($mapped = Arr::get($this->nameMap, $base)) {
            return $mapped;
        }

        return Str::plural(Str::snake($base)); // sensible default
    }
}
