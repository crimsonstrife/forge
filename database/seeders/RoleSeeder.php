<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Use configured (UUID) models:
        $Role = app(config('permission.models.role'));
        $Permission = app(config('permission.models.permission'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Ensure roles exist
        $super       = $Role::firstOrCreate(['name' => 'SuperAdmin',   'guard_name' => 'web']);
        $admin       = $Role::firstOrCreate(['name' => 'Admin',        'guard_name' => 'web']);
        $projectAdmin= $Role::firstOrCreate(['name' => 'ProjectAdmin', 'guard_name' => 'web']);
        $member      = $Role::firstOrCreate(['name' => 'Member',       'guard_name' => 'web']);
        $viewer      = $Role::firstOrCreate(['name' => 'Viewer',       'guard_name' => 'web']);

        // Helper to fetch permission names safely (created by PermissionSeeder)
        $get = static fn(array $names) => $Permission::query()
            ->whereIn('name', $names)
            ->pluck('name')
            ->all();

        // Assign permissions (dot-style)
        $super->syncPermissions(['is-super-admin']);

        $admin->syncPermissions($get([
            'is-admin',
            'admin.panel.access',
            'admin.connectors.manage',
            'admin.mappings.manage',
            // general app access
            'projects.view', 'projects.manage',
            'issues.view', 'issues.create', 'issues.update', 'issues.transition',
            'comments.create',
            'attachments.manage',
            // optional: if you gate Filament with a permission
            'filament.access',
        ]));

        $projectAdmin->syncPermissions($get([
            'projects.view', 'projects.manage',
            'issues.view', 'issues.create', 'issues.update', 'issues.transition',
            'comments.create',
            'attachments.manage',
        ]));

        $member->syncPermissions($get([
            'projects.view',
            'issues.view', 'issues.create', 'issues.update', 'issues.transition',
            'comments.create',
            'attachments.manage',
        ]));

        $viewer->syncPermissions($get([
            'projects.view',
            'issues.view',
        ]));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Auto-assign SuperAdmin to first user (global/no team)
        $firstUser = User::query()->oldest('id')->first();
        if ($firstUser && ! $firstUser->hasRole('SuperAdmin')) {
             $firstUser->assignRole('SuperAdmin');
        }
    }
}
