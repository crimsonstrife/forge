<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if there is already a super admin user, i.e., at least one user with the super-admin role
        $superAdmin = User::role('super-admin')->first();

        if ($superAdmin) {
            return;
        }

        // Generate a password using faker
        $password = $this->generatePassword();

        if (!$superAdmin) {
            // Create a super admin user
            User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'email' => 'admin@forge.app',
            'password' => $password,
            ]);

            // Assign the super admin role to the admin user
            $superAdmin = User::where('email', 'admin@forge.app')->first();

            if ($superAdmin) {
                $superAdmin->assignRole('super-admin');
            }
        }
    }

    /**
     * Generate a password using faker.
     *
     * @return string
     */
    private function generatePassword(): string
    {
        $faker = Faker::create();

        $password = $faker->password(8, 20);

        // Since the password will be unknown, and ideally temporary, log it to the console for the user
        $this->command->info('Admin password: ' . $password . ' (This password will be used to log in as the super admin user, you should change it.)');

        // Wait for the user to see the password
        sleep(10); // 10 seconds

        // Return the hashed password
        return bcrypt($password);
    }
}
