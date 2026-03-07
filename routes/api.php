<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectMemberController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//protected routes for projects
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
});

//protected routes for tasks
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/projects/{projectId}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{projectId}/tasks', [TaskController::class, 'store']);
    Route::get('/projects/{projectId}/tasks/{taskId}', [TaskController::class, 'show']);
    Route::put('/projects/{projectId}/tasks/{taskId}', [TaskController::class, 'update']);
    Route::delete('/projects/{projectId}/tasks/{taskId}', [TaskController::class, 'destroy']);
});

//protected routes for project members
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/projects/{projectId}/members', [ProjectMemberController::class, 'index']);
    Route::post('/projects/{projectId}/members', [ProjectMemberController::class, 'store']);
    Route::put('/projects/{projectId}/members/{membershipId}', [ProjectMemberController::class, 'update']);
    Route::delete('/projects/{projectId}/members/{membershipId}', [ProjectMemberController::class, 'destroy']);
});