<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Project;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Setup API endpoints for user data, project issues, repository details, and more.
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->get('/projects', function (Request $request, Project $project) {
    return $project->getProjects();
});
Route::middleware('auth:api')->get('/projects/{project}', function (Request $request, Project $project) {
    return $project;
});
Route::middleware('auth:api')->get('/projects/{project}/issues', function (Request $request, Project $project) {
    return $project->getIssues();
});
