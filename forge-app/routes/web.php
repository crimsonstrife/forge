<?php

use Illuminate\Support\Facades\Route;
use Laravel\Horizon\Http\Controllers\HomeController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\File;

/**
 * Define the route for the home page.
 *
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * Define routes for the Discord bot to handle interactions.
 * These routes are accessible via the '/discord' URL.
 */
Route::post('/discord/create-issue', [DiscordController::class, 'createIssue']); // Create a new issue from a Discord submission
Route::get('/discord/issue-status/{id}', [DiscordController::class, 'issueStatus']); // Get the status of an issue
Route::post('/discord/issue/{id}/approve', [DiscordController::class, 'approveIssue']); // Approve an issue, and assign it to a user and project
Route::post('/discord/issue/{id}/reject', [DiscordController::class, 'rejectIssue']); // Reject an issue

/**
 * Define routes for Discord acount linking.
 * These routes are accessible via the '/discord' URL.
 */
Route::get('/auth/discord', [DiscordAuthController::class, 'redirectToDiscord'])->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'handleDiscordCallback']);
Route::get('/auth/discord/unlink', [DiscordAuthController::class, 'unlinkDiscord'])->name('auth.discord.unlink');
Route::get('/discord/user/{id}', [DiscordAuthController::class, 'getUserDiscordId']);


/**
 * Route group for authenticated users with sanctum middleware, jetstream auth session, and verified user.
 *
 * @return \Illuminate\Routing\Router
 */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'landingPage'])->name('dashboard');
    Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->name('dashboards.view');
    Route::get('/dashboard/manage', [DashboardController::class, 'manage'])->name('dashboards.manage');
});

/**
 * Route group for authenticated users with elevated permissions using the RoleMiddleware.
 * This group is accessible via admin subdirectory of the /dashboard URL.
 * The RoleMiddleware will check if the user has the 'admin' role.
 */
Route::group([
    'middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:admin'],
    'prefix' => 'admin',
], function () {
    Route::get('/dashboard/admin', function () {
        return view('dashboard.admin');
    })->name('dashboard.admin');
});

/**
 * Route for verifying email address after registration.
 * uses the 'verification.verify' middleware.
 * This route is accessible via the '/email/verify/{id}/{hash}' URL.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
Route::get('/email/verify/{id}/{hash}', function () {
    return view('auth.verify-email');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'signed'])->name('verification.verify');

/**
 * Route for handling email verification notices.
 * This route is accessible via the '/email/verify' URL.
 * It uses the 'verification.notice' middleware.
 *
 * @return \Illuminate\Contracts\View\View
 */
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth:sanctum', config('jetstream.auth_session')])->name('verification.notice');

/**
 * Route to handle sending email verification link.
 * It uses the 'verification.send' middleware.
 * This route is accessible via the '/email/verify/send' URL.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
Route::get('/email/verify/send', function () {
    return view('auth.verify-email');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'throttle:6,1'])->name('verification.send');

/**
 * Route for handling email verification completion.
 * This route is accessible via the '/email/verify/complete' URL.
 * It uses the 'verification.complete' middleware.
 *
 * @return \Illuminate\Contracts\View\View
 */
Route::get('/email/verify/complete', function () {
    return view('auth.verify-email');
})->middleware(['auth:sanctum', config('jetstream.auth_session')])->name('verification.complete');

/**
 * Route to serve icon files from the public directory.
 * This route is accessible via the 'public/$path' URL with path always starting with 'icons', i.e public/icons/builtin/outline/academic-cap.svg.
 *
 * @param string $path
 *
 * @return \Illuminate\Http\Response
 */
Route::get('public/{path}', function ($path) {
    $path = public_path($path);
    if (File::exists($path)) {
        return response()->file($path);
    }
    return response()->json(['message' => 'File not found.'], 404);
})->where('path', 'icons.*');
