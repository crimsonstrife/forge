<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testRegistrationScreenCanBeRendered(): void
    {
        if (!Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function testRegistrationScreenCannotBeRenderedIfSupportIsDisabled(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function testNewUsersCanRegister(): void
    {
        if (!Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');
        }

        // Create a new user
        $username = $this->faker->userName;
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $response = $this->post('/register', [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated('web');
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
