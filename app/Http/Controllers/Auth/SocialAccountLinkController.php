<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

final class SocialAccountLinkController extends Controller
{
    /** @var array<int,string> */
    private array $allowedProviders;

    public function __construct()
    {
        $this->allowedProviders = config('services.socialite_providers', ['github', 'gitlab']);
    }

    public function redirect(Request $request, string $provider): RedirectResponse
    {
        $this->ensureProviderAllowed($provider);

        $driver = Socialite::driver($provider);

        // For self-hosted GitLab/Gitea, pass base_url if driver supports it
        if (config("services.{$provider}.base_url") && method_exists($driver, 'setHost')) {
            $driver->setHost(config("services.{$provider}.base_url"));
        }

        // Safe fallback for redirect URL
        $redirectUrl = config("services.{$provider}.redirect");
        if (empty($redirectUrl)) {
            // Build it dynamically at runtime (safe because we have a Request here)
            $redirectUrl = url("/auth/{$provider}/link/callback");
        }

        if (method_exists($driver, 'redirectUrl')) {
            $driver->redirectUrl($redirectUrl);
        }

        return $driver->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $this->ensureProviderAllowed($provider);

        $driver = Socialite::driver($provider);

        if (config("services.{$provider}.base_url") && method_exists($driver, 'setHost')) {
            $driver->setHost(config("services.{$provider}.base_url"));
        }

        $oauthUser = $driver->user();

        // Prevent linking to another user if already taken
        $existing = SocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', (string) $oauthUser->getId())
            ->first();

        if ($existing && $existing->user_id !== $request->user()->id) {
            throw ValidationException::withMessages([
                'provider' => __('This :provider account is already linked to another user.', ['provider' => ucfirst($provider)]),
            ]);
        }

        // Upsert for this user+provider
        /** @var User $user */
        $user = $request->user();

        $account = SocialAccount::query()
            ->updateOrCreate(
                ['user_id' => $user->id, 'provider' => $provider],
                [
                    'provider_user_id' => (string) $oauthUser->getId(),
                    'nickname' => $oauthUser->getNickname() ?: ($oauthUser->getName() ?: null),
                    'token' => $oauthUser->token ?? null,
                    'refresh_token' => $oauthUser->refreshToken ?? null,
                    'expires_at' => isset($oauthUser->expiresIn) ? now()->addSeconds((int) $oauthUser->expiresIn) : null,
                ]
            );

        return redirect()->route('profile.show')->with('status', __(':provider account linked.', ['provider' => ucfirst($provider)]));
    }

    public function destroy(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $this->authorize('delete', $socialAccount);
        if ($socialAccount->user_id !== $request->user()->id) {
            abort(403);
        }

        $socialAccount->delete();

        return redirect()->route('profile.show')->with('status', __('Account disconnected.'));
    }

    private function ensureProviderAllowed(string $provider): void
    {
        if (! in_array($provider, $this->allowedProviders, true)) {
            abort(404);
        }
    }
}
