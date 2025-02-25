<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\AttributeController;

// 🔑 Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
});

// 🔒 Protected routes with authentication
Route::middleware('auth:api')->group(function () {

    // 📁 Projects CRUD
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/users', [ProjectController::class, 'assignUserToProject']);
    Route::get('projects/filter', [ProjectController::class, 'filter']);
    Route::post('projects/{project}/attributes', [ProjectController::class, 'setAttributes']);

    // 👤 Users CRUD
    Route::apiResource('users', UserController::class);

    // 📝 Timesheets CRUD
    Route::apiResource('timesheets', TimesheetController::class);

    // 🏷️ Attributes CRUD
    Route::apiResource('attributes', AttributeController::class);
});
