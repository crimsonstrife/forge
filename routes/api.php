<?php

use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Webhooks\GitHubWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhooks/github', [GitHubWebhookController::class, 'handle'])
    ->name('webhooks.github');


Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public (throttled) ingest; optionally protect with 'ingest.key' later.
    Route::post('tickets', [V1\TicketController::class, 'store'])
        ->name('tickets.store')
        ->middleware(['ingest.key', 'throttle:ticket-ingest']);
});

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('me', V1\MeController::class)->name('api.v1.me');

    Route::get('projects', [V1\ProjectController::class, 'index'])->name('api.v1.projects.index');
    Route::get('projects/{project}', [V1\ProjectController::class, 'show'])->name('api.v1.projects.show');

    Route::get('lookups', V1\LookupsController::class);

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
