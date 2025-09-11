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

    /** @var array{permissions:array<int,string>} */
    public array $updateApiTokenForm = [
        'permissions' => [],
    ];

    public bool $displayingToken = false;
    public bool $managingApiTokenPermissions = false;
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

    public function manageApiTokenPermissions(int $tokenId): void
    {
        $token = $this->findTokenOrFail($tokenId);

        $this->managingApiTokenId = $token->id;
        $this->updateApiTokenForm['permissions'] = $token->abilities ?? [];
        $this->managingApiTokenPermissions = true;
    }

    public function updateApiToken(): void
    {
        if ($this->managingApiTokenId === null) {
            return;
        }

        $token = $this->findTokenOrFail($this->managingApiTokenId);
        $abilities = $this->sanitizeAbilities($this->updateApiTokenForm['permissions']);

        // Persist abilities
        $token->forceFill(['abilities' => $abilities])->save();

        $this->managingApiTokenPermissions = false;
        $this->managingApiTokenId = null;

        $this->user->unsetRelation('tokens');
        $this->user->load('tokens');
        $this->dispatch('saved');
    }

    public function confirmApiTokenDeletion(int $tokenId): void
    {
        $this->deletingTokenId = $tokenId;
        $this->confirmingApiTokenDeletion = true;
    }

    public function deleteApiToken(): void
    {
        if ($this->deletingTokenId === null) {
            return;
        }

        $token = $this->findTokenOrFail($this->deletingTokenId);
        $token->delete();

        $this->confirmingApiTokenDeletion = false;
        $this->deletingTokenId = null;

        $this->user->unsetRelation('tokens');
        $this->user->load('tokens');

        $this->dispatch('deleted');
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
