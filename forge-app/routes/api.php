<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Setup API endpoints for user data, project issues/tasks, repository details, and more.
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
