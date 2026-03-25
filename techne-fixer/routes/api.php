<?php

use App\Http\Controllers\Api\AuditLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ServiceTypeController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ContactController;

// ── Public Routes ─────────────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login',           [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
});

Route::prefix('services')->group(function () {
    Route::get('/',        [ServiceController::class, 'publicIndex']);
    Route::get('/{slug}',  [ServiceController::class, 'publicShow']);
});
Route::get('/portfolio', [PortfolioController::class, 'publicIndex']);
Route::post('/contact',  [ContactController::class, 'send']);

// ── Protected Routes (auth:sanctum) ───────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/logout',    [AuthController::class, 'logout']);
        Route::get('/user',       [AuthController::class, 'user']);
        Route::put('/profile',    [AuthController::class, 'updateProfile']);
        Route::put('/password',   [AuthController::class, 'updatePassword']);
    });

    Route::prefix('admin')->group(function () {
        Route::apiResource('/categories', CategoryController::class);
        Route::get('/service-types', [ServiceTypeController::class, 'index']);
        Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore']);

        Route::apiResource('/services', ServiceController::class);
        Route::patch('/services/{id}/restore', [ServiceController::class, 'restore']);
        Route::apiResource('/portfolio', PortfolioController::class);
        Route::patch('/portfolio/{id}/restore', [PortfolioController::class, 'restore']);
        Route::get('/audit-logs', [AuditLogController::class, 'index']);
        // Route::apiResource('/portfolio', PortfolioController::class);
    });

});