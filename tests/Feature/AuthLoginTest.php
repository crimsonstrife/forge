<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rejects_array_email_payloads_with_validation_errors(): void
    {
        Livewire::test(Login::class)
            ->set('email', ['bot@example.com'])
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => 'string']);
    }

    public function test_login_rejects_array_remember_payloads_with_validation_errors(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'jamie@example.com')
            ->set('password', 'password')
            ->set('remember', ['yes'])
            ->call('login')
            ->assertHasErrors(['remember' => 'boolean']);
    }

    public function test_login_accepts_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('remember', true)
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }
}
