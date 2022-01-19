<?php

use App\Cache\PermissionCache;
use App\Models\Dev\DevCategory;
use Illuminate\Support\Facades\Route;

Route::middleware(debugMiddleware())->prefix(PermissionCache::PDev)->group(function () {
    routePackCategory(
        PermissionCache::PDevCategory,
        DevCategory::class
    );
});
