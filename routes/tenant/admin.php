<?php

use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\AuthController;
use App\Http\Middleware\DebugMiddleware;

// Admin
use App\Http\Controllers\AdminControllers\AdminUserController;
use App\Http\Controllers\AdminControllers\AdminDepartmentController;
use App\Http\Controllers\AdminControllers\AdminPositionController;

// 为了测试方便, 不认证路由权限
$middlewares = [];
if (config('app.debug')) {
    $middlewares[] = DebugMiddleware::class;
} else {
    $middlewares[] = 'auth:sanctum';
}

Route::post('/login', [AuthController::class, 'login']);

Route::middleware($middlewares)->group(function () {
    Route::post('/userinfo', [AuthController::class, 'userinfo']);
    Route::prefix('/admin')->group(function () {
        Route::prefix('/user')->group(function () {
            Route::post('/find/{id}', [AdminUserController::class, 'find']);
            Route::post('/list', [AdminUserController::class, 'list']);
            Route::post('/', [AdminUserController::class, 'create']);
            Route::put('/{id}', [AdminUserController::class, 'update']);
            Route::delete('/', [AdminUserController::class, 'delete']);
            Route::post('/positions', [AdminUserController::class, 'positions']);
            Route::post('/status/{status}', [AdminUserController::class, 'status']);
        });
        Route::prefix('/department')->group(function () {
            Route::post('/find/{id}', [AdminDepartmentController::class, 'find']);
            Route::post('/list', [AdminDepartmentController::class, 'list']);
            Route::post('/', [AdminDepartmentController::class, 'create']);
            Route::put('/{id}', [AdminDepartmentController::class, 'update']);
            Route::delete('/{id}', [AdminDepartmentController::class, 'delete']);
        });
        Route::prefix('/position')->group(function () {
            Route::post('/find/{id}', [AdminPositionController::class, 'find']);
            Route::post('/list', [AdminPositionController::class, 'list']);
            Route::post('/', [AdminPositionController::class, 'create']);
            Route::put('/{id}', [AdminPositionController::class, 'update']);
            Route::delete('/{id}', [AdminPositionController::class, 'delete']);
            Route::post('/departments', [AdminPositionController::class, 'departments']);
            Route::post('/permissions', [AdminPositionController::class, 'permissions']);
        });
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