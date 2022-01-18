<?php

namespace App\Http\Controllers;

use App\Cache\PermissionCache;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Resources\UserinfoResource;
use App\Models\Admin\AdminRolePermissionName;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function userinfo()
    {
        /** @var AdminUser $user */
        $user = Auth::user();
        return result(new UserinfoResource($user));
    }

    function toReact($menus)
    {
        $newMenus = [];
        foreach ($menus as $menu) {
            if (count($menu['children']) > 0) {
                $menu['children'] = $this->toReact($menu['children']);
            }
            $newMenus[] = $menu;
        }
        return $newMenus;
    }

    function login(LoginRequest $request)
    {
        $remember = $request->input('remember');
        $phone    = $request->input('phone');
        $password = $request->input('password');
        $password = rsaDecrypt($password);
        $post     = [
            'phone'    => $phone,
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
        $token           = $user->createToken('admin');
        $permissionCache = new PermissionCache();
        if ($user->id === 1) {
            $authInfo = $permissionCache->getAuthInfo();
        } else {
            $ids      = $user->admin_role_ids()->toArray();
            $names    = AdminRolePermissionName::query()
                ->select(['permission_name'])
                ->whereIn('admin_role_id', $ids)
                ->pluck('permission_name')
                ->toArray();
            $authInfo = $permissionCache->getAuthInfo(sess('e_id'), $names);
        }
        $authInfo['menus'] = $this->toReact($authInfo['menus']);
        return result([
            'token'    => substr($token->plainTextToken, 3),
            'data'     => new UserinfoResource($user),
            'message'  => '登录成功',
            'authInfo' => $authInfo
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
