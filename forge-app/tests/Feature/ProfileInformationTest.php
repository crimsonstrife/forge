<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileInformationTest extends TestCase
{
    use RefreshDatabase;

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

        // If user returns as an int, it will fail so we need to pass the user object
        $actingUser = User::find($user->id);

        $this->actingAs($actingUser);

        Livewire::test(UpdateProfileInformationForm::class)
            ->set('state', ['username' => 'TestName', 'email' => 'test@example.com'])
            ->call('updateProfileInformation');

        $this->assertEquals('TestName', $user->fresh()->username);
        $this->assertEquals('test@example.com', $user->fresh()->email);
    }
}
