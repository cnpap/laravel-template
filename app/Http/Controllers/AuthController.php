<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\LoginRequest;
use App\Http\Resources\UserinfoResource;
use App\Models\Admin\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function userinfo()
    {
        /** @var AdminUser $user */
        $user = Auth::user();
        return result(new UserinfoResource($user));
    }

    function login(LoginRequest $request)
    {
        $remember = $request->input('remember');
        $phone    = $request->input('phone');
        $password = $request->input('password');
        $password = rsaDecrypt($password);
        $post     = [
            'phone'    => $request->input(),
            'password' => $password,
        ];
        $ok       = Auth::attempt($post, (bool)$remember);
        if (!$ok) {
            return se([
                'code'    => 403,
                'message' => '登录失败, 请检查输入后重试'
            ]);
        }
        /** @var AdminUser $user */
        $user = AdminUser::filter($request->validated())->first();
        $user->tokens()->delete();
        $token = $user->createToken('admin');
        return result([
            'token'   => substr($token->plainTextToken, 2),
            'data'    => new UserinfoResource($user),
            'message' => '登录成功'
        ]);
    }

    function logout()
    {
        /** @var AdminUser $user */
        $user = Auth::user();
        $user->tokens()->delete();
        return se([
            'code'    => 401,
            'message' => '开始跳转登录页面'
        ]);
    }
}
