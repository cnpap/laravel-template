<?php

namespace App\Http\Middleware;

use App\Models\Admin\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.debug') === true) {
            /** @var AdminUser $user */
            $user = AdminUser::query()->find(1);
            Auth::login($user);
        }
        return $next($request);
    }
}
