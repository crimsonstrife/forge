<?php

use Illuminate\Support\Facades\Route;
use Laravel\Horizon\Http\Controllers\HomeController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Auth\DiscordAuthController;
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
 * Route to serve icon files from the resource directory.
 * This route is accessible via the 'resource/icons/{type}/{style}/{file}' URL.
 *
 * @param string $type
 * @param string $style
 * @param string $file
 *
 * @return \Illuminate\Http\Response
 */
Route::get('/icons/builtin/{type}/{style}/{file}', function ($type, $style, $file) {
    $path = resource_path("icons/builtin/{$type}/{$style}/{$file}");

    if (!File::exists($path)) {
        logger()->error("File not found: " . $path); // Log the path for debugging
        abort(404, "File not found: " . $path); // Show the full file path in the 404 message
    }

    return response()->file($path);
})->name('icon.builtin');

/**
 * Route for fetching icon svg files/code
 * This route is accessible via the '/icon/{id}/svg' URL.
 *
 * @param int $id
 *
 */
Route::get('/icon/{id}/svg', function ($id) {
    $icon = \App\Models\Icon::findOrFail($id);

    // Check if the svg_file_path is valid (not null and not empty)
    if (!empty($icon->svg_file_path)) {
        // prioritize the SVG file path
        return response()->file(storage_path("app/public/{$icon->svg_file_path}"));
    }

    // Return the SVG code, sanitized
    if (!empty($icon->svg_code)) {
        $sanitizer = app(\App\Services\SvgSanitizerService::class);
        return new \Illuminate\Http\Response($sanitizer->sanitize($icon->svg_code), 200, ['Content-Type' => 'image/svg+xml']);
    }

    // Return a 404 error if the icon does not have an SVG file path or SVG code
    return response()->json(['svg' => ''], 404);
})->name('icon.svg');
