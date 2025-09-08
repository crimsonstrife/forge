<?php

use App\Http\Controllers\Webhooks\GitHubWebhookController;
use App\Http\Controllers\IssueAttachmentDownloadController;
use App\Http\Controllers\ProjectCalendarController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', static function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get(
        '/projects/{project}/issues/{issue}/attachments/{media}',
        IssueAttachmentDownloadController::class
    )->name('issues.attachments.download');
    Route::get('/projects/{project}/calendar.ics', ProjectCalendarController::class)
        ->middleware(['auth','verified'])
        ->name('projects.calendar.ics');
});


Route::get('/auth/github/redirect', [GitHubAuthController::class, 'redirect'])
    ->name('auth.github.redirect');

Route::get('/auth/github/callback', [GitHubAuthController::class, 'callback'])
    ->name('auth.github.callback');

Route::post('/webhooks/github', [GitHubWebhookController::class, 'handle'])
    ->name('webhooks.github');
