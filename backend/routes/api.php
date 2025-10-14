<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Product routes
    Route::get('/products/statistics', [ProductController::class, 'statistics']);
    Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
    Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
    Route::apiResource('products', ProductController::class);
});

// Admin only routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // User management routes
    Route::get('/users/statistics', [UserController::class, 'statistics']);
    Route::get('/users/verified', [UserController::class, 'verified']);
    Route::get('/users/unverified', [UserController::class, 'unverified']);
    Route::get('/users/admins', [UserController::class, 'getAdmins']);
    Route::patch('/users/{id}/role', [UserController::class, 'updateRole']);
    Route::apiResource('users', UserController::class);
});
