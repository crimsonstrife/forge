<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Manages Sanctum Personal Access Tokens for the authenticated user.
 */
final class ApiManager extends Component
{
    public User $user;

    /** @var array{name:string,permissions:array<int,string>} */
    #[Validate(['createApiTokenForm.name' => 'required|string|max:80'])]
    public array $createApiTokenForm = [
        'name' => '',
        'permissions' => [],
    ];

    public bool $displayingToken = false;
    public bool $managingApiTokenPermissions = false;
    public array $updateApiTokenForm = ['permissions' => []];
    public ?PersonalAccessToken $editingToken = null;
    public bool $confirmingApiTokenDeletion = false;

    public ?int $managingApiTokenId = null;
    public ?int $deletingTokenId = null;

    public string $plainTextToken = '';

    public function mount(): void
    {
        /** @var User $u */
        $u = auth()->user();
        $this->user = $u;
        $this->user->loadMissing('tokens');
    }

    public function render(): View
    {
        return view('api.api-token-manager');
    }

    public function createApiToken(): void
    {
        $this->validate();

        $abilities = $this->sanitizeAbilities(
            $this->createApiTokenForm['permissions'] ?: Jetstream::$defaultApiTokenPermissions
        );

        $token = $this->user->createToken($this->createApiTokenForm['name'], $abilities);
        $this->plainTextToken = $token->plainTextToken;
        $this->displayingToken = true;

        // reset form + refresh tokens list
        $this->createApiTokenForm = ['name' => '', 'permissions' => []];
        $this->user->unsetRelation('tokens');
        $this->user->load('tokens');

        // keep Blade's <x-action-message on="created">
        $this->dispatch('created');
    }

    public function manageApiTokenPermissions(string $tokenId): void
    {
        $this->editingToken = PersonalAccessToken::query()->findOrFail($tokenId);

        // Abilities are stored as JSON; ensure array in form.
        $this->updateApiTokenForm['permissions'] = $this->editingToken->abilities ?? [];

        $this->managingApiTokenPermissions = true;
    }

    public function updateApiToken(): void
    {
        $this->authorize('update', $this->editingToken); // optional, if you have a policy

        $abilities = array_values(array_filter($this->updateApiTokenForm['permissions'] ?? [], 'strlen'));

        $this->editingToken->forceFill(['abilities' => $abilities])->save();

        $this->dispatch('saved'); // or $this->dispatch('created') if your UI listens for it
        $this->managingApiTokenPermissions = false;
    }

    public function confirmApiTokenDeletion(string $tokenId): void
    {
        $this->editingToken = PersonalAccessToken::query()->findOrFail($tokenId);
        $this->confirmingApiTokenDeletion = true;
    }

    public function deleteApiToken(): void
    {
        optional($this->editingToken)->delete();
        $this->confirmingApiTokenDeletion = false;
        $this->editingToken = null;

        // Refresh the user's tokens if you show them live
        $this->user->refresh();
    }

    /** @return array<int,string> */
    private function sanitizeAbilities(array $requested): array
    {
        $allowed = Jetstream::$permissions ?? [];
        return array_values(array_intersect($requested, $allowed));
    }

    private function findTokenOrFail(int $id): PersonalAccessToken
    {
        /** @var PersonalAccessToken $token */
        $token = $this->user->tokens()->whereKey($id)->firstOrFail();

        return $token;
    }
}
