<?php

use App\Http\Controllers\CommonControllers\LogController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\AuthController;
use App\Http\Middleware\DebugMiddleware;

// Admin
use App\Http\Controllers\AdminControllers\AdminUserController;
use App\Http\Controllers\AdminControllers\AdminDepartmentController;
use App\Http\Controllers\AdminControllers\AdminPositionController;
use App\Http\Controllers\AdminControllers\AdminRoleController;

// Enterprise
use App\Http\Controllers\EnterpriseControllers\AuthController as EnterpriseAuthController;

// 为了测试方便, 不认证路由权限
$middlewares = [];
if (config('app.debug')) {
    $middlewares[] = DebugMiddleware::class;
} else {
    $middlewares[] = 'auth:sanctum';
}

Route::post('/login', [AuthController::class, 'login']);
Route::post('/region', [RegionController::class, 'region']);
Route::post('/child_regions', [RegionController::class, 'childRegions']);
Route::post('/stop_log/{id}', [LogController::class, 'stopLog']);

Route::middleware($middlewares)->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/userinfo', [AuthController::class, 'userinfo']);
    Route::prefix('/enterprise')->group(function () {
        Route::post('/upload/zj', [EnterpriseAuthController::class, 'uploadZj']);
        Route::post('/upload/id', [EnterpriseAuthController::class, 'uploadId']);
        Route::post('/upload/mm', [EnterpriseAuthController::class, 'uploadMm']);
        Route::post('/upload/hj', [EnterpriseAuthController::class, 'uploadHj']);
    });
    Route::prefix('/admin')->group(function () {
        Route::prefix('/user')->group(function () {
            Route::post('/find/{id}', [AdminUserController::class, 'find']);
            Route::post('/list', [AdminUserController::class, 'list']);
            Route::post('/create', [AdminUserController::class, 'create']);
            Route::put('/{id}', [AdminUserController::class, 'update']);
            Route::delete('/delete', [AdminUserController::class, 'delete']);
            Route::post('/positions', [AdminUserController::class, 'positions']);
            Route::post('/status', [AdminUserController::class, 'status']);
            Route::post('/enabled_list', [AdminUserController::class, 'enabledList']);
            Route::post('/forgot_password/{id}', [AdminUserController::class, 'forgotPassword']);
            Route::post('/status', [AdminUserController::class, 'status']);
        });
        Route::prefix('/department')->group(function () {
            Route::post('/find/{id}', [AdminDepartmentController::class, 'find']);
            Route::post('/list', [AdminDepartmentController::class, 'list']);
            Route::post('/create', [AdminDepartmentController::class, 'create']);
            Route::put('/{id}', [AdminDepartmentController::class, 'update']);
            Route::delete('/delete', [AdminDepartmentController::class, 'delete']);
            Route::post('/status', [AdminDepartmentController::class, 'status']);
        });
        Route::prefix('/position')->group(function () {
            Route::post('/find/{id}', [AdminPositionController::class, 'find']);
            Route::post('/list', [AdminPositionController::class, 'list']);
            Route::post('/create', [AdminPositionController::class, 'create']);
            Route::put('/{id}', [AdminPositionController::class, 'update']);
            Route::delete('/delete', [AdminPositionController::class, 'delete']);
            Route::post('/status', [AdminPositionController::class, 'status']);
            Route::post('/departments', [AdminPositionController::class, 'departments']);
            Route::post('/permissions', [AdminPositionController::class, 'permissions']);
        });
        Route::prefix('/role')->group(function () {
            Route::post('/find/{id}', [AdminRoleController::class, 'find']);
            Route::post('/list', [AdminRoleController::class, 'list']);
            Route::post('/create', [AdminRoleController::class, 'create']);
            Route::put('/{id}', [AdminRoleController::class, 'update']);
            Route::delete('/delete', [AdminRoleController::class, 'delete']);
            Route::post('/status', [AdminRoleController::class, 'status']);
            Route::post('/permission_table', [AdminRoleController::class, 'permissionTable']);
            Route::post('/sync_permission_names', [AdminRoleController::class, 'syncPermissionNames']);
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
