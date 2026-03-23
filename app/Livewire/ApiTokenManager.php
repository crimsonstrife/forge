<?php

namespace App\Livewire;

use Laravel\Jetstream\Http\Livewire\ApiTokenManager as BaseApiTokenManager;
use Laravel\Jetstream\Jetstream;
use Laravel\Passport\PersonalAccessTokenResult;

class ApiTokenManager extends BaseApiTokenManager
{
    /**
     * Display the token value to the user.
     *
     * Overridden to handle Passport's PersonalAccessTokenResult, which carries
     * the plain-text token in ->accessToken (a JWT string) rather than
     * Sanctum's ->plainTextToken ("id|token" format).
     */
    protected function displayTokenValue($token): void
    {
        $this->displayingToken = true;

        if ($token instanceof PersonalAccessTokenResult) {
            $this->plainTextToken = $token->accessToken;
        } else {
            // Sanctum NewAccessToken — original Jetstream behaviour
            $this->plainTextToken = explode('|', $token->plainTextToken, 2)[1] ?? $token->plainTextToken;
        }

        $this->dispatch('showing-token-modal');
    }

    /**
     * Open the permissions management modal for a token.
     *
     * Overridden because Passport tokens use ->scopes (array) not ->abilities.
     */
    public function manageApiTokenPermissions($tokenId): void
    {
        $this->managingApiTokenPermissions = true;

        $this->managingPermissionsFor = $this->user->tokens()
            ->where('id', $tokenId)
            ->firstOrFail();

        // Passport Token: scopes. Sanctum PersonalAccessToken: abilities.
        $this->updateApiTokenForm['permissions'] = $this->managingPermissionsFor->scopes
            ?? $this->managingPermissionsFor->abilities
            ?? [];
    }

    /**
     * Update the token's permissions.
     *
     * Overridden because Passport tokens use the `scopes` column, not `abilities`.
     * Updating `scopes` in the DB is sufficient — Passport reads scopes from the
     * database on every authenticated request, not from the JWT body.
     */
    public function updateApiToken(): void
    {
        $valid = Jetstream::validPermissions((array) $this->updateApiTokenForm['permissions']);

        $this->managingPermissionsFor->forceFill([
            'scopes' => $valid,
        ])->save();

        $this->managingApiTokenPermissions = false;
    }
}
