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
            header("Access-Control-Allow-Origin: *");
            /** @var AdminUser $user */
            $user = AdminUser::query()->find('_super_manager');
            Auth::login($user);

            $eid = Session::get('eid');
            if (!$eid) {
                Session::put('eid', 'admin');
            }
        }
        return $next($request);
    }
}
