<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Livewire\Livewire;
use Tests\TestCase;

class BrowserSessionsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testOtherBrowserSessionsCanBeLoggedOut(): void
    {
        $user = User::factory()->create();

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        // Create Session Token information with Faker
        $tokenId = $this->faker->uuid;
        $tokenName = $this->faker->word;
        $ipAddress = $this->faker->ipv4;
        $clientID = $this->faker->randomNumber(5, true);

        // Create a new session token
        $actingUser->tokens()->create([
            'id' => $tokenId,
            'client_id' => $clientID,
            'user_id' => $actingUser->id,
            'name' => $tokenName,
            'revoked' => false,
        ]);

        // Start the session
        $this->withSession(['_token' => 'test']);

        // Log out other browser sessions
        Livewire::test(LogoutOtherBrowserSessionsForm::class)
            ->call('logoutOtherBrowserSessions')->set('password', 'password')->call('logoutOtherBrowserSessions')->assertSuccessful();

        // Assert that all but the current session token have been revoked
        $isTokenRevoked = $actingUser->tokens()
            ->where('id', '!=', $tokenId)
            ->where('name', '!=', $tokenName)
            ->where('user_id', $actingUser->id)
            ->where('revoked', true)
            ->exists();

        $this->assertTrue($isTokenRevoked);

        // Assert that the user is still logged in
        $this->assertAuthenticated();
    }
}
