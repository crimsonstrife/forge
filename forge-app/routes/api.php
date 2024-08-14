<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Project;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RepositoryController;

/**
 * Retrieve the authenticated user.
 *
 * This route returns the authenticated user using the `Request` object.
 * The user is retrieved by calling the `user()` method on the `Request` object.
 * The route is protected by the `auth:sanctum` middleware.
 *
 * @param \Illuminate\Http\Request $request The request object.
 *
 * @return \Illuminate\Contracts\Auth\Authenticatable|null The authenticated user or null if not authenticated.
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/**
 * API Routes for the authenticated user.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Contracts\Auth\Authenticatable|null
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * API Routes for Projects and Project Repositories
 *
 * @middleware auth:api
 *
 * @route GET /projects
 * @controller ProjectController@index
 * @description Get all projects
 *
 * @route POST /projects
 * @controller ProjectController@store
 * @description Create a new project
 *
 * @route GET /projects/{id}
 * @controller ProjectController@show
 * @description Get a specific project by ID
 *
 * @route PUT /projects/{id}
 * @controller ProjectController@update
 * @description Update a specific project by ID
 *
 * @route DELETE /projects/{id}
 * @controller ProjectController@destroy
 * @description Delete a specific project by ID
 *
 * @route GET /projects/{id}/repositories
 * @controller RepositoryController@index
 * @description Get all repositories for a specific project
 *
 * @route POST /projects/{id}/repositories
 * @controller RepositoryController@store
 * @description Create a new repository for a specific project
 *
 * @route GET /projects/{id}/repositories/{repositoryId}
 * @controller RepositoryController@show
 * @description Get a specific repository for a specific project by ID
 *
 * @route PUT /projects/{id}/repositories/{repositoryId}
 * @controller RepositoryController@update
 * @description Update a specific repository for a specific project by ID
 *
 * @route DELETE /projects/{id}/repositories/{repositoryId}
 * @controller RepositoryController@destroy
 * @description Delete a specific repository for a specific project by ID
 */
Route::middleware('auth:api')->group(function () {
    // Project routes
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

    // Project Repository routes
    Route::get('/projects/{id}/repositories', [RepositoryController::class, 'index']);
    Route::post('/projects/{id}/repositories', [RepositoryController::class, 'store']);
    Route::get('/projects/{id}/repositories/{repositoryId}', [RepositoryController::class, 'show']);
    Route::put('/projects/{id}/repositories/{repositoryId}', [RepositoryController::class, 'update']);
    Route::delete('/projects/{id}/repositories/{repositoryId}', [RepositoryController::class, 'destroy']);
});

/**
 * Handle the Crucible webhook.
 *
 * @param  \App\Http\Controllers\RepositoryController  $controller
 * @param  string  $method
 * @return \Illuminate\Http\Response
 */
Route::post('/webhooks/crucible', [RepositoryController::class, 'handleCrucibleWebhook']);
