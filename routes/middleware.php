<?php

use App\Http\Middleware\DebugMiddleware;

// 为了测试方便, 不认证路由权限
$middlewares = [];
if (config('app.debug')) {
    $middlewares[] = DebugMiddleware::class;
} else {
    $middlewares[] = 'auth:sanctum';
}

return $middlewares;
