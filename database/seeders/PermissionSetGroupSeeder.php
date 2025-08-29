<?php

namespace Database\Seeders;

use App\Models\PermissionSet;
use App\Models\PermissionSetGroup;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSetGroupSeeder extends Seeder
{
    public function run(): void
    {
        $PermissionSet = PermissionSet::class;
        $PermissionSetGroup = PermissionSetGroup::class;

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $engineering = $PermissionSetGroup::query()->firstOrCreate(['name' => 'Engineering']);
        $support     = $PermissionSetGroup::query()->firstOrCreate(['name' => 'Support']);

        $developerId = $PermissionSet::query()->where('name', 'Developer')->value('id');
        $restrictedId = $PermissionSet::query()->where('name', 'Restricted Reporter')->value('id');

        if ($developerId) {
            $engineering->permissionSets()->syncWithoutDetaching([$developerId]);
        }
        if ($restrictedId) {
            $support->permissionSets()->syncWithoutDetaching([$restrictedId]);
        }
    }
}
