<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UserController, AddProductsController, AdminController};
use App\Http\Middleware\CheckAdmin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/products', [AddProductsController::class, 'index']); 

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::prefix('products')->group(function () {
        Route::post('/', [AddProductsController::class, 'store']);
        Route::delete('/{id}', [AddProductsController::class, 'destroy']);
    });


    Route::middleware(CheckAdmin::class)->prefix('admin')->group(function () {

        Route::get('/data', [AdminController::class, 'index']);

        Route::get('/users/{id}/products', [AdminController::class, 'getUserProducts']);

        Route::prefix('users/{id}')->group(function () {
            Route::post('/make-moderator', [AdminController::class, 'makeModerator']);
            Route::post('/make-admin', [AdminController::class, 'makeAdmin']);
            Route::post('/make-super-admin', [AdminController::class, 'makeSuperAdmin']);

            Route::delete('/', [AdminController::class, 'destroyUser']);
        });

        Route::delete('/products/{id}', [AdminController::class, 'deleteProduct']);
    });
});
