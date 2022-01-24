<?php

use App\Cache\PermissionCache;
use App\Http\Controllers\AdminControllers\AdminOrganizationController;
use App\Http\Controllers\CommonControllers\LogController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

// Auth
use App\Http\Controllers\AuthController;

// Admin
use App\Http\Controllers\AdminControllers\AdminUserController;
use App\Http\Controllers\AdminControllers\AdminDepartmentController;
use App\Http\Controllers\AdminControllers\AdminPositionController;
use App\Http\Controllers\AdminControllers\AdminRoleController;

// Enterprise
use App\Http\Controllers\EnterpriseControllers\AuthController as EnterpriseAuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/region', [RegionController::class, 'region']);
Route::post('/child_regions', [RegionController::class, 'childRegions']);
Route::post('/stop_log/{id}', [LogController::class, 'stopLog']);

Route::middleware(debugMiddleware())->group(function () {
    routePack(
        null,
        AuthController::class,
        ['logout', 'userinfo']
    );
    Route::prefix('admin')->group(function () {
        routePack(
            PermissionCache::PAdminOrganization,
            AdminOrganizationController::class,
        );
        routePack(
            PermissionCache::PEnterprise,
            EnterpriseAuthController::class,
            ['uploadZj', 'uploadId', 'uploadMm', 'uploadHj']
        );
        routePack(
            PermissionCache::PAdminUser,
            AdminUserController::class,
            [
                'departmentOptions',
                'positionOptions',
                'roleOptions',
                'forgotPassword'    => '$/{id}',
                'syncOrganizations' => '$/{id}',
                'findOrganizations' => '$/{id}'
            ]
        );
        routePack(
            PermissionCache::PAdminDepartment,
            AdminDepartmentController::class
        );
        routePack(
            PermissionCache::PAdminPosition,
            AdminPositionController::class,
            [
                'departmentOptions',
            ]
        );
        routePack(
            PermissionCache::PAdminRole,
            AdminRoleController::class,
            [
                'syncPermissionNames' => '$/{id}',
                'findPermissionNames' => '$/{id}'
            ]
        );
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
