<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionSet;
use App\Models\PermissionSetGroup;
use App\Models\Role;
use App\Models\Team;
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
        $Role = app(config('permission.models.role'));
        $Permission = app(config('permission.models.permission'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /** create permissions (guard MUST match) */
        $names = [
            'projects.view',
            'projects.manage',
            'issues.view',
            'issues.create',
            'issues.update',
            'issues.delete',
            'issues.transition',
            'comments.create',
            'comments.update',
            'comments.delete',
            'attachments.manage',
            'admin.panel.access',
            'admin.connectors.manage',
            'admin.mappings.manage',
            'is-admin',
            'is-super-admin'];
        foreach ($names as $n) {
            $Permission::firstOrCreate(['name' => $n, 'guard_name' => 'web']);
        }

        // Roles
        $super  = $Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $admin  = $Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $member = $Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $viewer = $Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $proj = $Role::firstOrCreate(['name' => 'ProjectAdmin', 'guard_name' => 'web']);

        $super->syncPermissions(['is-super-admin']);
        $admin->syncPermissions(['is-admin','admin.panel.access','admin.connectors.manage','admin.mappings.manage',
            'projects.view','projects.manage','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage']);
        $proj->syncPermissions(['projects.view','projects.manage','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage']);
        $member->syncPermissions(['projects.view','issues.view','issues.create','issues.update','issues.transition','comments.create','attachments.manage']);
        $viewer->syncPermissions(['projects.view','issues.view']);

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

        // First user bootstrap
        $firstUser = User::query()->oldest('id')->first();
        if ($firstUser && ! $firstUser->hasRole('SuperAdmin')) {
            // With Teams enabled, set the team context before assigning:
            $registrar = app(PermissionRegistrar::class);

            // Pick/create a team id that exists:
            $teamId = class_exists(Team::class)
                ? (Team::query()->value('id') ?? Team::factory()->create(['name' => 'Default'])->id)
                : 1; // fallback if you use a custom team table/key

            $registrar->setPermissionsTeamId($teamId);
            $firstUser->assignRole('SuperAdmin');
            $registrar->setPermissionsTeamId(null);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
