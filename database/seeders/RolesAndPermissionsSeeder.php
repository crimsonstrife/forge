<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionSet;
use App\Models\PermissionSetGroup;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permissions
        $perms = [
            'projects.view', 'projects.manage',
            'issues.view', 'issues.create', 'issues.update', 'issues.delete', 'issues.transition',
            'comments.create', 'comments.update', 'comments.delete',
            'attachments.manage',
            'admin.panel.access', 'admin.connectors.manage', 'admin.mappings.manage',
            'is-admin', 'is-super-admin',
        ];

        $permModels = collect($perms)->mapWithKeys(function (string $name) {
            $p = Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            return [$name => $p];
        });

        // Roles
        $super = Role::query()->firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $admin = Role::query()->firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $proj  = Role::query()->firstOrCreate(['name' => 'ProjectAdmin', 'guard_name' => 'web']);
        $member= Role::query()->firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $viewer= Role::query()->firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);

        $super->givePermissionTo(['is-super-admin']);
        $admin->givePermissionTo(['is-admin','admin.panel.access','admin.connectors.manage','admin.mappings.manage',
            'projects.view','projects.manage','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage']);
        $proj->givePermissionTo(['projects.view','projects.manage','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage']);
        $member->givePermissionTo(['projects.view','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage']);
        $viewer->givePermissionTo(['projects.view','issues.view']);

        // Permission Sets
        $developer = PermissionSet::query()->firstOrCreate(['name' => 'Developer'], [
            'description' => 'Typical engineer capabilities',
        ]);
        $developer->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('name', [
                'projects.view','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage'
            ])->pluck('id')
        );

        $restrictedReporter = PermissionSet::query()->firstOrCreate(['name' => 'Restricted Reporter'], [
            'description' => 'Reporters without delete or attachment rights',
        ]);
        $restrictedReporter->permissions()->syncWithoutDetaching(
            Permission::query()->whereIn('name', ['projects.view','issues.view','issues.create','comments.create'])->pluck('id')
        );
        $restrictedReporter->mutedPermissions()->syncWithoutDetaching(
            Permission::query()->whereIn('name', ['issues.delete','comments.delete','attachments.manage'])->pluck('id')
        );

        // Attach sets to roles
        $developer->roles()->syncWithoutDetaching([$member->id]);
        // Optionally: an "Admin Toolkit" set you can attach to Admin role, etc.

        // Optional groups
        $engineering = PermissionSetGroup::query()->firstOrCreate(['name' => 'Engineering']);
        $engineering->permissionSets()->syncWithoutDetaching([$developer->id]);

        // First user bootstrap (if desired)
        $firstUser = User::query()->oldest('id')->first();
        if ($firstUser && ! $firstUser->hasRole('SuperAdmin')) {
            $firstUser->assignRole('SuperAdmin');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
