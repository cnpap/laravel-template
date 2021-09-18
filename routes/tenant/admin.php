<?php

use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\AuthController;

// AdminUser
use App\Http\Controllers\AdminUserController;

// 为了测试方便, 不认证路由权限
$middlewares = [];
if (!config('app.debug')) {
    $middlewares[] = 'auth:sanctum';
}

Route::post('/login', [AuthController::class, 'login']);

Route::middleware($middlewares)->prefix('/admin')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::post('/find/{id}', [AdminUserController::class, 'find']);
        Route::post('/list', [AdminUserController::class, 'list']);
        Route::post('/', [AdminUserController::class, 'create']);
        Route::put('/{id}', [AdminUserController::class, 'update']);
        Route::delete('/{id}', [AdminUserController::class, 'delete']);
    });
});

// 放在最下面, 在不变动 laravel 原有认证模块下, 假装自己是 spa,
// 通过 300+ 状态重定位到当前这个控制器, 然后再以 json 的形式返回 403 信息
Route::get('/login', function () {
    return se(['status' => 403, 'message' => '请重新认证']);
})->name('login');

Route::middleware('auth:sanctum')->get('/hi', function () {
    // 在 debug 的情况下的测试路由
    return 'hello word';
});