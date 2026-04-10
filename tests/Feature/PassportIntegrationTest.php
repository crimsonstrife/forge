<?php

namespace Tests\Feature;

use App\Filament\Resources\OAuthClients\Pages\CreateOAuthClient;
use App\Models\Permission;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PassportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_passport_authorization_view_response_renders_the_custom_authorization_view(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $client = Client::query()->create([
            'name' => 'Crucible SSO',
            'secret' => 'top-secret-value',
            'provider' => 'users',
            'redirect' => 'https://crucible.test/oauth/callback',
            'redirect_uris' => ['https://crucible.test/oauth/callback'],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'revoked' => false,
        ]);

        $request = Request::create('/oauth/authorize', 'GET', ['state' => 'test-state']);

        $response = app(AuthorizationViewResponse::class)
            ->withParameters([
                'client' => $client,
                'user' => $user,
                'scopes' => [],
                'request' => $request,
                'authToken' => 'test-auth-token',
            ])
            ->toResponse($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Authorization Request', $response->getContent());
        $this->assertStringContainsString('Crucible SSO', $response->getContent());
        $this->assertStringContainsString($user->email, $response->getContent());
    }

    public function test_creating_an_oauth_client_notifies_with_the_plaintext_secret(): void
    {
        $user = $this->makePanelUser();

        Filament::setCurrentPanel('admin');

        $component = Livewire::actingAs($user)
            ->test(CreateOAuthClient::class)
            ->fillForm([
                'name' => 'Crucible SSO',
                'redirect' => 'https://crucible.test/oauth/callback',
                'revoked' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $client = Client::query()->where('name', 'Crucible SSO')->firstOrFail();
        $secret = $component->instance()->record->plainSecret;

        $this->assertNotNull($secret);
        $this->assertTrue(Hash::isHashed($client->getRawOriginal('secret')));
        $this->assertTrue(Hash::check($secret, $client->getRawOriginal('secret')));

        $component->assertNotified(
            FilamentNotification::make()
                ->title('Client Created — Copy Your Secret')
                ->body("Client Secret: {$secret}\n\nThis secret will not be shown again. Store it securely in your Codex FORGE_CLIENT_SECRET environment variable.")
                ->warning()
                ->persistent()
        );
    }

    private function makePanelUser(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        Permission::firstOrCreate([
            'name' => 'is-super-admin',
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $user->givePermissionTo('is-super-admin');
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        return $user;
    }
}
