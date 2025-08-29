<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Always clear Spatie cache first to avoid stale grants.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // --- Core auth scaffolding (order matters) ---
        $this->call([
            PermissionSeeder::class,        // creates all permissions
            RoleSeeder::class,              // creates roles & attaches permissions
            PermissionSetSeeder::class,     // creates permission sets & attaches permissions
            PermissionSetGroupSeeder::class // creates groups & attaches sets
        ]);

        // --- Domain defaults / enums (call if present) ---
        $this->callIfExists(IssueEnumsSeeder::class);
        //$this->callIfExists(RepositoriesSeeder::class);
        //$this->callIfExists(GithubDefaultsSeeder::class);
        //$this->callIfExists(GiteaDefaultsSeeder::class);
        //$this->callIfExists(SyncDefaultsSeeder::class);

        // --- Bootstrap admin / first user (interactive by default) ---
        // Tip: run with --no-interaction to auto-generate credentials per your UserSeeder.
        $this->call(UserSeeder::class);

        // Re-cache Spatie after everything.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function callIfExists(string $seeder): void
    {
        if (class_exists($seeder)) {
            $this->call($seeder);
        }
    }
}
