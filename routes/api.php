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

Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('me', V1\MeController::class)->name('api.v1.me');

    Route::get('projects', [V1\ProjectController::class, 'index'])->name('api.v1.projects.index');
    Route::get('projects/{project}', [V1\ProjectController::class, 'show'])->name('api.v1.projects.show');

    Route::get('lookups', \App\Http\Controllers\Api\V1\LookupsController::class)
        ->middleware(['auth:sanctum','throttle:api']);

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
});
