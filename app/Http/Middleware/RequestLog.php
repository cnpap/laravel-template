<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestLog
{
    public function handle(Request $request, Closure $next, $type)
    {
        $label  = $request->method() . '_' . $request->path();
        $userId = Auth::id();
        Log::channel('request_' . $type)->info($label, [
            'user id' => $userId,
            'ip'      => $request->ip(),
            'heads'   => $request->header(),
            'body'    => $request->post()
        ]);
        return $next($request);
    }
}
