<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUserCommand extends Command
{
    protected $signature = 'make:user';
    protected $description = 'Create a new user';

    public function handle(): void
    {
        $name = $this->ask('What is the user\'s name?');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->secret('What is the user\'s password?');

        // Validate email format
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Invalid email address.');
            return;
        }

        // Check if the user already exists
        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email address already exists.');
            return;
        }

        try {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
            return;
        }
        $this->info('User created successfully!');
    }
}
