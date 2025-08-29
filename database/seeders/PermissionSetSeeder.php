<?php

namespace Database\Seeders;

use App\Models\PermissionSet;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSetSeeder extends Seeder
{
    public function run(): void
    {
        $PermissionSet = PermissionSet::class;
        $Permission = app(config('permission.models.permission'));
        $Role = app(config('permission.models.role'));

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Helper to fetch permission IDs by name
        $ids = static fn(array $names) => $Permission::query()
            ->whereIn('name', $names)
            ->pluck('id')
            ->all();

        // Developer set
        $developer = $PermissionSet::query()->firstOrCreate(
            ['name' => 'Developer'],
            ['description' => 'Typical engineer capabilities']
        );

        $developer->permissions()->syncWithoutDetaching($ids([
            'projects.view',
            'issues.view', 'issues.create', 'issues.update', 'issues.transition',
            'comments.create',
            'attachments.manage',
        ]));

        // Restricted Reporter (mutes deletes & attachments)
        $restricted = $PermissionSet::query()->firstOrCreate(
            ['name' => 'Restricted Reporter'],
            ['description' => 'Reporters without delete or attachment rights']
        );

        $restricted->permissions()->syncWithoutDetaching($ids([
            'projects.view',
            'issues.view', 'issues.create',
            'comments.create',
        ]));

        $restricted->mutedPermissions()->syncWithoutDetaching($ids([
            'issues.delete',
            'comments.delete',
            'attachments.manage',
        ]));

        // Attach sets to roles (example: Developer -> Member)
        $memberRoleId = $Role::query()->where('name', 'Member')->value('id');
        if ($memberRoleId) {
            $developer->roles()->syncWithoutDetaching([$memberRoleId]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
