<?php

use Illuminate\Support\Facades\Route;
use Laravel\Horizon\Http\Controllers\HomeController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Auth\DiscordAuthController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
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
 * Route for accessing the Horizon dashboard.
 *
 * This route maps to the 'index' method of the 'HomeController' class.
 * It is accessible via the '/horizon' URL and has the name 'horizon.index'.
 *
 * @return \Illuminate\Contracts\View\View
 */
Route::get('/horizon', [HomeController::class, 'index'])->name('horizon.index');
