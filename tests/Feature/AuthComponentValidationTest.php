<?php

namespace Tests\Feature;

use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthComponentValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_rejects_array_email_payloads_with_validation_errors(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Jamie Customer')
            ->set('email', ['jamie@example.com'])
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email' => 'string']);
    }

    public function test_forgot_password_rejects_array_email_payloads_with_validation_errors(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', ['jamie@example.com'])
            ->call('sendPasswordResetLink')
            ->assertHasErrors(['email' => 'string']);
    }

    public function test_confirm_password_rejects_array_password_payloads_with_validation_errors(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ConfirmPassword::class)
            ->set('password', ['super-secret'])
            ->call('confirmPassword')
            ->assertHasErrors(['password' => 'string']);
    }

    public function test_reset_password_rejects_array_email_payloads_with_validation_errors(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('email', ['jamie@example.com'])
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('resetPassword')
            ->assertHasErrors(['email' => 'string']);
    }

    public function test_reset_password_mount_ignores_array_email_query_params(): void
    {
        Livewire::withQueryParams(['email' => ['jamie@example.com']])
            ->test(ResetPassword::class, ['token' => 'test-token'])
            ->assertSet('email', '');
    }
}
