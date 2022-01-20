<?php

namespace App\Http\Middleware;

use App\Models\Admin\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DebugMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.debug') === true) {
            $user = Auth::user();
            if ($user === null) {
                /** @var AdminUser $user */
                $user = AdminUser::query()->find(1);
                Auth::login($user);

                $tenantCode = tenantCode();
                if (!$tenantCode) {
                    Session::put('tenantCode', 'admin');
                }
            }
        }
        return $next($request);
    }
}
