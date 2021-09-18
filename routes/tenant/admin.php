<?php

use Illuminate\Support\Facades\Route;

// AdminUser
use App\Http\Controllers\AdminUserController;

$middlewares = [];
if (!config('app.debug')) {
    $middlewares[] = 'auth:sanctum';
}

Route::middleware($middlewares)->prefix('/admin')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::post('/find/{id}', [AdminUserController::class, 'find']);
        Route::post('/list', [AdminUserController::class, 'list']);
        Route::post('/', [AdminUserController::class, 'create']);
        Route::put('/{id}', [AdminUserController::class, 'update']);
        Route::delete('/{id}', [AdminUserController::class, 'delete']);
    });
});