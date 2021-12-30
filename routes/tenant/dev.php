<?php

use App\Models\Dev\DevCategory;
use Illuminate\Support\Facades\Route;

Route::middleware(require __DIR__ . '/../middleware.php')->prefix('dev')->group(function () {
    routePackCategory('category', DevCategory::class);
});
