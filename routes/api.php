<?php

use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Api\V1\SystemIssueController;
use App\Http\Controllers\Api\V1\SystemOrganizationController;
use App\Http\Controllers\Api\V1\SystemProjectController;
use App\Http\Controllers\Webhooks\GitHubWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:api,sanctum');

Route::post('/webhooks/github', [GitHubWebhookController::class, 'handle'])
    ->name('webhooks.github');

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public (throttled) ingest; optionally protect with 'ingest.key' later.
    Route::post('tickets', [V1\TicketController::class, 'store'])
        ->name('tickets.store')
        ->middleware(['ingest.key', 'throttle:ticket-ingest']);

    Route::get('/public/projects/{slug}/issues', [V1\PublicProjectIssuesController::class, 'index'])
        ->name('public.projects.issues')
        ->middleware('throttle:60,1');
});

/*
|--------------------------------------------------------------------------
| System API — machine-to-machine (OAuth2 client credentials)
|--------------------------------------------------------------------------
|
| These routes are called by trusted external applications (e.g. Codex)
| using a client credentials token. No user context is available.
|
| Generate a client with:
|   php artisan passport:client --client --name="Codex M2M"
|
| Then set FORGE_M2M_CLIENT_ID / FORGE_M2M_CLIENT_SECRET in the Codex .env.
|
*/
Route::prefix('v1/system')->name('api.v1.system.')->middleware(['client:projects:read', 'throttle:api'])->group(function (): void {
    Route::get('projects', [SystemProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{project}', [SystemProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/issues', [SystemIssueController::class, 'index'])->name('projects.issues.index');

    Route::get('organizations', [SystemOrganizationController::class, 'index'])->name('organizations.index');
    Route::get('organizations/{organization}', [SystemOrganizationController::class, 'show'])->name('organizations.show');
});

/*
|--------------------------------------------------------------------------
| User API — authenticated user (Passport PAT or Sanctum)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(['auth:api,sanctum', 'throttle:api'])->group(function (): void {
    Route::get('me', V1\MeController::class)->name('api.v1.me');

    Route::get('projects', [V1\ProjectController::class, 'index'])->name('api.v1.projects.index');
    Route::get('projects/{project}', [V1\ProjectController::class, 'show'])->name('api.v1.projects.show');

    Route::get('lookups', V1\LookupsController::class);
    Route::get('mentions/users', [V1\MentionsController::class, 'users'])->name('api.v1.mentions.users');
    Route::get('mentions/issues', [V1\MentionsController::class, 'issues'])->name('api.v1.mentions.issues');

    Route::get('issues', [V1\IssueController::class, 'index'])->name('api.v1.issues.index');
    Route::post('issues', [V1\IssueController::class, 'store'])->name('api.v1.issues.store');
    Route::get('issues/{issue:id}', [V1\IssueController::class, 'show'])->name('api.v1.issues.show');
    Route::put('issues/{issue:id}', [V1\IssueController::class, 'update'])->name('api.v1.issues.update');

    Route::post('issues/{issue:id}/transition', [V1\IssueTransitionController::class, 'store'])
        ->name('api.v1.issues.transition');

    Route::post('issues/{issue:id}/comments', [V1\IssueCommentController::class, 'store'])
        ->name('api.v1.issues.comments.store');

    Route::post('issues/{issue:id}/attachments', [V1\IssueAttachmentController::class, 'store'])
        ->name('api.v1.issues.attachments.store');

    Route::get('issues/{issue:id}/time/summary', [V1\IssueTimeController::class, 'summary'])
        ->name('api.v1.issues.time.summary');

    Route::post('issues/{issue:id}/time/start', [V1\IssueTimeController::class, 'start'])
        ->name('api.v1.issues.time.start');

    Route::post('issues/{issue:id}/time/stop', [V1\IssueTimeController::class, 'stop'])
        ->name('api.v1.issues.time.stop');

    Route::post('issues/{issue:id}/time', [V1\IssueTimeController::class, 'store'])
        ->name('api.v1.issues.time.store');
});
