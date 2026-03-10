<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\CommentsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    // Projects
    Route::prefix('projects')->group(function () {
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('{project}', [ProjectController::class, 'show']);
        Route::put('{project}', [ProjectController::class, 'update']);
        Route::delete('{project}', [ProjectController::class, 'destroy']);

        // Project Tasks
        Route::get('{project}/tasks', [TaskController::class, 'index']);
        Route::post('{project}/tasks', [TaskController::class, 'store']);
        Route::get('{project}/tasks/{task}', [TaskController::class, 'show']);
        Route::put('{project}/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('{project}/tasks/{task}', [TaskController::class, 'destroy']);

        // Project Members
        Route::get('{project}/members', [ProjectMemberController::class, 'index']);
        Route::post('{project}/members', [ProjectMemberController::class, 'store']);
        Route::put('{project}/members/{membership}', [ProjectMemberController::class, 'update']);
        Route::delete('{project}/members/{membership}', [ProjectMemberController::class, 'destroy']);
    });

    // Tasks
    Route::prefix('tasks')->group(function () {

        // Task Assignments
        Route::get('{task}/assignments', [TaskAssignmentController::class, 'index']);
        Route::get('{task}/assignments/{assignment}', [TaskAssignmentController::class, 'show']);
        Route::post('{task}/assignments', [TaskAssignmentController::class, 'store']);
        Route::delete('{task}/assignments/{assignment}', [TaskAssignmentController::class, 'destroy']);

        // Task Comments
        Route::get('{task}/comments', [CommentsController::class, 'index']);
        Route::get('{task}/comments/{comment}', [CommentsController::class, 'show']);
        Route::post('{task}/comments', [CommentsController::class, 'store']);
        Route::delete('{task}/comments/{comment}', [CommentsController::class, 'destroy']);
    });

});