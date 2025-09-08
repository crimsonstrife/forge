<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')
            ->scopes(['repo', 'read:org'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $githubUser = Socialite::driver('github')->user();
        $user = Auth::user();

        // Store or update linked account
        SocialAccount::updateOrCreate(
            [
                'provider' => 'github',
                'provider_user_id' => $githubUser->id,
                'user_id' => $user->id,
            ],
            [
                'nickname' => $githubUser->nickname,
                'token' => $githubUser->token,
                'refresh_token' => $githubUser->refreshToken ?? null,
                'expires_at' => $githubUser->expiresIn
                    ? now()->addSeconds($githubUser->expiresIn)
                    : null,
            ]
        );

        return redirect()
            ->route('dashboard')
            ->with('status', 'GitHub account connected successfully.');
    }
}
