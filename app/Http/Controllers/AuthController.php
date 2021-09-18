<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\LoginRequest;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function login(LoginRequest $request)
    {
        $remember = $request->input('remember');
        $ok       = Auth::attempt($request->validated(), (bool)$remember);
        if (!$ok) {
            return se([
                'status'  => 403,
                'message' => '登录失败, 请检查输入后重试'
            ]);
        }
        /** @var AdminUser $user */
        $user = AdminUser::filter($request->validated())->first();
        $user->tokens()->delete();
        $token = $user->createToken('admin');
        return ss([
            'token'   => $token->plainTextToken,
            'message' => '登录成功'
        ]);
    }

    function logout()
    {
        /** @var AdminUser $user */
        $user = Auth::user();
        $user->tokens()->delete();
        return se([
            'status'  => 401,
            'message' => '开始跳转登录页面'
        ]);
    }
}