<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Livewire\Livewire;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordCanBeUpdated(): void
    {
        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->call('updatePassword');

        $this->assertTrue(Hash::check('new-password', $actingUser->fresh()->password));
    }

    public function testCurrentPasswordMustBeCorrect(): void
    {
        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);

        $this->assertTrue(Hash::check('password', $actingUser->fresh()->password));
    }

    public function testNewPasswordsMustMatch(): void
    {
        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'wrong-password',
            ])
            ->call('updatePassword')
            ->assertHasErrors(['password']);

        $this->assertTrue(Hash::check('password', $actingUser->fresh()->password));
    }
}
