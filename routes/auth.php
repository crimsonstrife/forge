<?php

use App\Http\Controllers\Auth\GitHubAuthController;
use App\Http\Controllers\Auth\SocialAccountLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/auth/{provider}/link', [SocialAccountLinkController::class, 'redirect'])
        ->name('social.link');

    Route::get('/auth/{provider}/link/callback', [SocialAccountLinkController::class, 'callback'])
        ->name('social.link.callback');

    Route::delete('/profile/social/{socialAccount}', [SocialAccountLinkController::class, 'destroy'])
        ->name('social.unlink');

    Route::get('/auth/github/redirect', [GitHubAuthController::class, 'redirect'])
        ->name('auth.github.redirect');

    Route::get('/auth/github/callback', [GitHubAuthController::class, 'callback'])
        ->name('auth.github.callback');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
