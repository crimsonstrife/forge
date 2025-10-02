<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class ReportPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // First or create with a UUID for the 'id' field
        $perm = Permission::firstOrCreate(
            ['name' => 'view.reports', 'guard_name' => 'web'],
            ['id' => Str::uuid()]
        );

        // Attach to common roles if they exist
        foreach (['Super Admin', 'Admin', 'Manager'] as $roleName) {
            if ($role = Role::where('name', $roleName)->first()) {
                $role->givePermissionTo($perm);
            }
        }
    }
}
