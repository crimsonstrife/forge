<?php

use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Api\V1\Feedback;
use App\Http\Controllers\Api\V1\Feedback\Admin;
use App\Http\Controllers\Api\V1\SystemIssueController;
use App\Http\Controllers\Api\V1\SystemOrganizationController;
use App\Http\Controllers\Api\V1\SystemProjectController;
use App\Http\Controllers\Sentry\AlertRuleOptionsController;
use App\Http\Controllers\Sentry\AlertRuleSettingsController;
use App\Http\Controllers\Webhooks\GitHubWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:api,sanctum');

Route::post('/webhooks/github', [GitHubWebhookController::class, 'handle'])
    ->name('webhooks.github');

Route::webhooks('/webhooks/sentry', 'sentry');

Route::prefix('sentry/options')
    ->name('sentry.options.')
    ->middleware('sentry.installation')
    ->group(function (): void {
        Route::get('projects', [AlertRuleOptionsController::class, 'projects'])->name('projects');
        Route::get('issue-types', [AlertRuleOptionsController::class, 'issueTypes'])->name('issue-types');
        Route::get('priorities', [AlertRuleOptionsController::class, 'priorities'])->name('priorities');
    });

Route::post('sentry/alert-rule', [AlertRuleSettingsController::class, 'store'])
    ->name('sentry.alert-rule.store')
    ->middleware('sentry.hook');

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

/*
|--------------------------------------------------------------------------
| Feedback API
|--------------------------------------------------------------------------
*/
Route::prefix('v1/feedback')
    ->name('api.v1.feedback.')
    ->middleware(['ingest.key', 'throttle:feedback'])
    ->group(function (): void {
        Route::post('auth/request', Feedback\AuthRequestController::class)
            ->middleware('throttle:feedback-auth')
            ->name('auth.request');
        Route::post('auth/verify', Feedback\AuthVerifyController::class)
            ->name('auth.verify');
        Route::post('auth/logout', Feedback\AuthLogoutController::class)
            ->middleware('feedback.identity')
            ->name('auth.logout');
        Route::get('auth/me', Feedback\AuthMeController::class)
            ->middleware('feedback.identity')
            ->name('auth.me');

        Route::get('boards/{slug}', [Feedback\BoardController::class, 'show'])
            ->middleware('feedback.identity.optional')
            ->name('boards.show');
        Route::get('boards/{slug}/posts', [Feedback\PostController::class, 'index'])
            ->middleware('feedback.identity.optional')
            ->name('boards.posts.index');
        Route::post('boards/{slug}/posts', [Feedback\PostController::class, 'store'])
            ->middleware(['feedback.identity', 'throttle:feedback-post'])
            ->name('boards.posts.store');

        Route::get('posts/{post}', [Feedback\PostController::class, 'show'])
            ->middleware('feedback.identity.optional')
            ->name('posts.show');
        Route::patch('posts/{post}', [Feedback\PostController::class, 'update'])
            ->middleware('feedback.identity')
            ->name('posts.update');
        Route::delete('posts/{post}', [Feedback\PostController::class, 'destroy'])
            ->middleware('feedback.identity')
            ->name('posts.destroy');
        Route::post('posts/{post}/vote', [Feedback\PostVoteController::class, 'store'])
            ->middleware(['feedback.identity', 'throttle:feedback-vote'])
            ->name('posts.vote');
        Route::get('posts/{post}/comments', [Feedback\CommentController::class, 'index'])
            ->middleware('feedback.identity.optional')
            ->name('posts.comments.index');
        Route::post('posts/{post}/comments', [Feedback\CommentController::class, 'store'])
            ->middleware(['feedback.identity', 'throttle:feedback-comment'])
            ->name('posts.comments.store');

        Route::patch('comments/{comment}', [Feedback\CommentController::class, 'update'])
            ->middleware('feedback.identity')
            ->name('comments.update');
        Route::delete('comments/{comment}', [Feedback\CommentController::class, 'destroy'])
            ->middleware('feedback.identity')
            ->name('comments.destroy');
        Route::post('comments/{comment}/vote', [Feedback\CommentVoteController::class, 'store'])
            ->middleware(['feedback.identity', 'throttle:feedback-vote'])
            ->name('comments.vote');
    });

Route::prefix('v1/feedback/admin')
    ->name('api.v1.feedback.admin.')
    ->middleware(['auth:api,sanctum', 'throttle:api'])
    ->group(function (): void {
        Route::get('boards', [Admin\AdminBoardController::class, 'index'])->name('boards.index');
        Route::post('boards', [Admin\AdminBoardController::class, 'store'])->name('boards.store');
        Route::patch('boards/{board}', [Admin\AdminBoardController::class, 'update'])->name('boards.update');
        Route::get('boards/{board}/posts', [Admin\AdminPostController::class, 'index'])->name('boards.posts.index');

        Route::patch('posts/{post}', [Admin\AdminPostController::class, 'update'])->name('posts.update');
        Route::post('posts/{post}/status', [Admin\AdminPostController::class, 'status'])->name('posts.status');
        Route::post('posts/{post}/pin', [Admin\AdminPostController::class, 'pin'])->name('posts.pin');
        Route::post('posts/{post}/merge', [Admin\AdminPostController::class, 'merge'])->name('posts.merge');
        Route::post('posts/{post}/convert-issue', [Admin\AdminPostController::class, 'convertIssue'])->name('posts.convert-issue');
        Route::post('posts/{post}/comments', [Admin\AdminPostController::class, 'comments'])->name('posts.comments.store');

        Route::patch('identities/{identity}/block', [Admin\AdminIdentityController::class, 'block'])->name('identities.block');
        Route::delete('comments/{comment}', [Admin\AdminCommentController::class, 'destroy'])->name('comments.destroy');
    });
