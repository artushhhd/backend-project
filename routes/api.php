<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UserController, AddProductsController, AdminController};
use App\Http\Middleware\CheckAdmin;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/products', [AddProductsController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    // Личный профиль и выход
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/products', [AddProductsController::class, 'store']);
    Route::delete('/products/{id}', [AddProductsController::class, 'destroy']);

    // Панель управления (Admin / Super Admin / Moderator)
    Route::middleware(CheckAdmin::class)->prefix('admin')->group(function () {

        Route::get('/data', [AdminController::class, 'index']);
        Route::get('/users/{id}/products', [AdminController::class, 'getUserProducts']);

        Route::post('/users/{id}/make-moderator', [AdminController::class, 'makeModerator']); // ДОБАВЛЕНО
        Route::post('/users/{id}/make-admin', [AdminController::class, 'makeAdmin']);
        Route::post('/users/{id}/make-super-admin', [AdminController::class, 'makeSuperAdmin']);

        Route::delete('/users/{id}', [AdminController::class, 'destroyUser']);
        Route::delete('/products/{id}', [AdminController::class, 'deleteProduct']);
    });
});
