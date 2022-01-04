<?php

use App\Models\Dev\DevCategory;
use Illuminate\Support\Facades\Route;

Route::middleware(debugMiddleware())->prefix('dev')->group(function () {
    routePackCategory('category', DevCategory::class);
});
