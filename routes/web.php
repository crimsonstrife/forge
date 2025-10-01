<?php

use App\Http\Controllers\Admin\ExportsController;
use App\Http\Controllers\HealthCheckResultsController;
use App\Http\Controllers\IssueActionItemController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueVcsController;
use App\Http\Controllers\Notifications\MarkAllReadController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueAttachmentController;
use App\Http\Controllers\ProjectCalendarController;
use App\Http\Controllers\TransitionStatusController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('/admin/exports/{record}/download', [ExportsController::class, 'download'])
        ->middleware('signed')
        ->name('admin.exports.download');
});

require __DIR__ . '/auth.php';

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', static function () {
        return view('dashboard');
    })->name('dashboard');
    Route::post('/notifications/mark-all-read', MarkAllReadController::class)
        ->name('notifications.markAllRead');
    Route::get('/status', HealthCheckResultsController::class)->name('status');
    Route::get(
        '/projects/{project}/issues/{issue}/attachments/{media}/download',
        [IssueAttachmentController::class, 'download']
    )->name('issues.attachments.download');
    Route::post('/projects/{project}/issues/{issue}/action-items/toggle', [IssueActionItemController::class, 'toggle'])
        ->name('issues.action-items.toggle');
    Route::post('/projects/{project}/issues/{issue}/transition', TransitionStatusController::class)
        ->name('issues.transition');
    Route::delete(
        '/projects/{project}/issues/{issue}/attachments/{media}',
        [IssueAttachmentController::class, 'destroy']
    )->name('issues.attachments.destroy');
    Route::get('/projects/{project}/calendar.ics', ProjectCalendarController::class)
        ->middleware(['auth', 'verified'])
        ->name('projects.calendar.ics');
    Route::delete('/projects/{project}', ProjectController::class)
        ->name('projects.destroy');

    Route::delete('/projects/{project}/issues/{issue}', IssueController::class)
        ->name('issues.destroy');
});

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/issues/{issue}/vcs/branches', [IssueVcsController::class, 'searchBranches'])->name('issues.vcs.branches.search');
    Route::get('/issues/{issue}/vcs/pulls', [IssueVcsController::class, 'searchPulls'])->name('issues.vcs.pulls.search');
    Route::get('/issues/{issue}/vcs/default-branch', [IssueVcsController::class, 'defaultBranch'])->name('issues.vcs.default-branch');
    Route::post('/issues/{issue}/vcs/link/branch', [IssueVcsController::class, 'linkBranch'])->name('issues.vcs.link.branch');
    Route::post('/issues/{issue}/vcs/link/pr', [IssueVcsController::class, 'linkPr'])->name('issues.vcs.link.pr');
    Route::post('/issues/{issue}/vcs/create/branch', [IssueVcsController::class, 'createBranch'])->name('issues.vcs.create.branch');
    Route::post('/issues/{issue}/vcs/create/pr', [IssueVcsController::class, 'createPr'])->name('issues.vcs.create.pr');
});
