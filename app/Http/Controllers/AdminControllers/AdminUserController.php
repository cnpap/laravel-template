<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserEditRequest;
use App\Http\Requests\Admin\AdminUserIndexRequest;
use App\Http\Requests\PasswordRequest;
use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    function forgotPassword(PasswordRequest $request, $id)
    {
        $password = $request->input('password');
        $password = Hash::make($password);
        AdminUser::query()
            ->where('id', $id)
            ->update([
                'password' => $password
            ]);
        return ss();
    }

    function status()
    {
        AdminUser::status();
        return ss();
    }

    function positions()
    {
        $positions = AdminDepartment::query()
            ->select([
                'id',
                'name'
            ])
            ->with(
                'positions:id,name,admin_department_id',
            )
            ->get();
        return result($positions);
    }

    function find($id)
    {
        $user = AdminUser::query()
            ->select([
                'id',
                'status',
                'phone',
                'email',
                'gender',
                'username',
                'admin_position_id'
            ])
            ->where('id', $id)
            ->firstOrFail();
        return result($user);
    }

    function list(AdminUserIndexRequest $request)
    {
        $paginator = AdminUser::indexFilter($request->validated())
            ->with('position.department:id,name')
            ->with('position:id,name,admin_department_id')
            ->paginate(...usePage());

        return page($paginator);
    }

    function enabledList(AdminUserIndexRequest $request)
    {
        $paginator = AdminUser::indexFilter($request->validated())
            ->with('position.department:id,name')
            ->with('position:id,name,admin_department_id')
            ->whereIn('status', ['新数据', '已使用'])
            ->paginate(...usePage());

        return page($paginator);
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
            if (isset($post['password'])) {
                $post['password'] = bcrypt($post['password']);
            }
            AdminUser::query()->where('id', $id)->update($post);
            return true;
        });
        if ($ok === true) {
            return ss();
        }
        return se();
    }

    function delete()
    {
        AdminUser::clear();
        return ss();
    }
}
