<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    public function testUserAccountsCanBeDeleted(): void
    {
        if (!Features::hasAccountDeletionFeatures()) {
            $this->markTestSkipped('Account deletion is not enabled.');
        }

        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        $component = Livewire::test(DeleteUserForm::class)
            ->set('password', 'password')
            ->call('deleteUser');

        $this->assertNull($user->fresh());
    }

    public function testCorrectPasswordMustBeProvidedBeforeAccountCanBeDeleted(): void
    {
        if (!Features::hasAccountDeletionFeatures()) {
            $this->markTestSkipped('Account deletion is not enabled.');
        }

        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        Livewire::test(DeleteUserForm::class)
            ->set('password', 'wrong-password')
            ->call('deleteUser')
            ->assertHasErrors(['password']);

        $this->assertNotNull($user->fresh());
    }
}
