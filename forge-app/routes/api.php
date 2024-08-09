<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Project;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RepositoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Setup API endpoints for user data, project issues, repository details, and more.
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
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
Route::middleware('auth:api')->get('/projects/{project}', function (Request $request, Project $project) {
    return $project;
});
Route::middleware('auth:api')->get('/projects/{project}/issues', function (Request $request, Project $project) {
    return $project->getIssues();
});
