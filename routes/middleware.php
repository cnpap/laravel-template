<?php

use App\Http\Middleware\DebugMiddleware;

// 为了测试方便, 不认证路由权限
function debugMiddleware($middlewares = [])
{
    if (config('app.debug')) {
        array_unshift($middlewares, DebugMiddleware::class);
    } else {
        array_unshift($middlewares, 'auth:sanctum');
    }

    return $middlewares;
}
