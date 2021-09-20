<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserEditRequest;
use App\Http\Requests\Admin\AdminUserIndexRequest;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminUser;
use Illuminate\Database\Eloquent\Builder;

class AdminUserController extends Controller
{
    function find($id)
    {
        $user = AdminUser::query()->findOrFail($id);
        return ss($user);
    }

    function list(AdminUserIndexRequest $request)
    {
        $result = AdminUser::page(
            $request,
            function (Builder $query) {
                $query->with(['adminDepartment']);
            }
        );
        return ss($result);
    }

    function create(AdminUserEditRequest $request)
    {
        // 分离参数
        $post       = $request->validated();
        $positionId = $post['admin_position_id'];

        // 开始事务
        $user           = new AdminUser($request->validated());
        $user->id       = uni();
        $user->password = bcrypt($user->password);
        $ok             = $user->getConnection()->transaction(function () use (
            $user,
            // 关联字段
            $positionId
        ) {
            // 联级状态
            AdminPosition::used($positionId);

            // 自身
            $user->save();
            return true;
        });
        if ($ok === true) {
            return ss();
        }
        return se();
    }

    function update(AdminUserEditRequest $request, $id)
    {
        // 分离参数
        $post       = $request->validated();
        $positionId = $post['admin_position_id'];

        // 开始事务
        $user = new AdminUser();
        $ok   = $user->getConnection()->transaction(function () use (
            $id, $post,
            // 关联字段
            $positionId
        ) {
            // 联级状态
            AdminPosition::used($positionId);

            // 自身
            $post['password'] = bcrypt($post['password']);
            AdminUser::query()->where('id', $id)->update($post);
            return true;
        });
        if ($ok === true) {
            return ss();
        }
        return se();
    }

    function delete($id)
    {
        AdminUser::clear($id);
        return ss();
    }
}
