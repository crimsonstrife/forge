<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testCurrentProfileInformationIsAvailable(): void
    {
        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        $component = Livewire::test(UpdateProfileInformationForm::class);

        $this->assertEquals($user->username, $component->state['username']);
        $this->assertEquals($user->email, $component->state['email']);
    }

    public function testProfileInformationCanBeUpdated(): void
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        // Generate a new username and email using faker
        $newUsername = $this->faker->userName;
        info('New username: ' . $newUsername);
        $newEmail = $this->faker->safeEmail;
        info('New email: ' . $newEmail);

        // Log the old username and email
        info('Old username: ' . $actingUser->username);
        info('Old email: ' . $actingUser->email);

        // Update the acting user's profile information
        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['username' => $newUsername, 'email' => $newEmail])
            ->call('updateProfileInformation');

        // Refresh the acting user to get the latest data
        $actingUser->refresh();

        // Log the updated username and email
        info('Updated username: ' . $actingUser->username);
        info('Updated email: ' . $actingUser->email);

        // Assert that the user's username and email have been updated
        $this->assertEquals($newUsername, $actingUser->username);
        $this->assertEquals($newEmail, $actingUser->email);
    }
}
