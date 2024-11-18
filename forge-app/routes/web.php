<?php

use Illuminate\Support\Facades\Route;
use Laravel\Horizon\Http\Controllers\HomeController;
use App\Http\Controllers\DiscordController;
use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportTemplateController;
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
 * Define routes for Discord account linking.
 * These routes are accessible via the '/discord' URL.
 */
Route::get('/auth/discord', [DiscordAuthController::class, 'redirectToDiscord'])->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'handleDiscordCallback']);
Route::get('/auth/discord/unlink', [DiscordAuthController::class, 'unlinkDiscord'])->name('auth.discord.unlink');
Route::get('/discord/user/{id}', [DiscordAuthController::class, 'getUserDiscordId']);

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
        return view('dashboards.admin');
    })->name('dashboards.admin');
});

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
    // Dashboard routes
    //Specific routes for authenticated users
    Route::get('/dashboard/manage', [DashboardController::class, 'manage'])->name('dashboards.manage');
    Route::get('/dashboard/create', [DashboardController::class, 'create'])->name('dashboards.create');

    // Route to catch-all the dashboard ids
    Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->name('dashboards.show');

    // Route to store the dashboard when creating a new one
    Route::post('/dashboards', [DashboardController::class, 'store'])->name('dashboards.store');

    // General dashboard route
    Route::get('/dashboard', [DashboardController::class, 'landingPage'])->name('dashboard');

    // Report routes
    Route::get('/dashboard/{dashboardId}/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/dashboard/{dashboardId}/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/dashboard/{dashboardId}/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/dashboard/{dashboardId}/reports/{id}', [ReportController::class, 'show'])->name('reports.show');

    // Template routes
    Route::prefix('templates')->group(function () {
        // Report template routes
        Route::prefix('reports')->name('templates.reports.')->group(function () {
            Route::get('/', [ReportTemplateController::class, 'index'])->name('index');
            Route::get('/create', [ReportTemplateController::class, 'create'])->name('create');
            Route::post('/', [ReportTemplateController::class, 'store'])->name('store');
            Route::get('/{id}', [ReportTemplateController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ReportTemplateController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ReportTemplateController::class, 'update'])->name('update');
            Route::delete('/{id}', [ReportTemplateController::class, 'destroy'])->name('destroy');
        });

        // Other template routes can be added here
    });
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

/**
 * Define a fallback route that will be executed when no other routes match.
 * This is useful for handling 404 errors and displaying a custom error page.
 */
Route::fallback(function () {
    \Log::error('Fallback route triggered', ['url' => request()->url()]);
    abort(404);
});
