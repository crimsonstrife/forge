<?php

namespace Database\Seeders;

//use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder class
 * Extends Seeder
 *
 * @category Seeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //User::factory()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        //]);

        //Seed the permissions
        $this->call(PermissionSeeder::class);
        //Seed the permission sets
        $this->call(PermissionSetSeeder::class);
        //Seed the permission groups
        $this->call(PermissionGroupSeeder::class);
        //Seed the roles
        $this->call(RoleSeeder::class);
        //Seed the users
        $this->call(UserSeeder::class);
        //Seed the icons
        $this->call(IconSeeder::class);
        //Seed the Issue Seeder Group
        $this->call(IssueRelatedSeederGroup::class); // Contains seeders related to Issue models.
    }
}
