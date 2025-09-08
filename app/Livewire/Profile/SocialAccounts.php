<?php
namespace App\Livewire\Profile;

use App\Models\SocialAccount;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class SocialAccounts extends Component
{
    /** @var array<int,string> */
    public array $providers = [];

    /** @var array<int,SocialAccount> */
    public array $accounts = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->providers = config('services.socialite_providers', ['github', 'gitlab']);
        $this->accounts  = $user?->socialAccounts()->get()->all() ?? [];
    }

    public function render(): View
    {
        // Explicitly pass data as Blade variables
        return view('livewire.profile.social-accounts', [
            'providers' => $this->providers,
            'accounts'  => $this->accounts,
        ]);
    }
}
