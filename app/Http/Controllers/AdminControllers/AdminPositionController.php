<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPositionEditRequest;
use App\Http\Requests\Admin\AdminPositionIndexRequest;
use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPermission;
use App\Models\Admin\AdminPosition;

class AdminPositionController extends Controller
{
    function departments()
    {
        $departments = AdminDepartment::query()->select(['id', 'name'])->get();
        return result($departments);
    }

    function permissions()
    {
        $permissions = AdminPermission::query()->get();
        return result($permissions);
    }

    function find($id)
    {
        /** @var AdminPosition $one */
        $one                         = AdminPosition::query()
            ->select([
                'id',
                'status',
                'name',
                'description',
                'admin_department_id',
            ])
            ->where('id', $id)
            ->firstOr();
        $one['admin_permission_ids'] = $one->permissions()->get()->modelKeys();
        return result($one);
    }

    function list(AdminPositionIndexRequest $request)
    {
        $paginator = AdminPosition::indexFilter($request->validated())
            ->with('department:id,name')
            ->paginate(...usePage());
        return page($paginator);
    }

    function create(AdminPositionEditRequest $request)
    {
        // 分离参数
        $post          = $request->validated();
        $permissionIds = $post['admin_permission_ids'];
        $departmentId  = $post['admin_department_id'];
        unset($post['admin_permission_ids']);

        // 事务开始
        $one     = new AdminPosition($post);
        $one->id = uni();
        $ok      = $one->getConnection()->transaction(function () use (
            $one, $post,
            // 关联字段
            $permissionIds,
            $departmentId
        ) {
            // 联级数据状态变更到已使用
            // 权限默认是 USED
            AdminDepartment::used($departmentId);

            // 变更自身
            $one->permissions()->sync($permissionIds);
            $one->save();
            return true;
        });
        if ($ok === true) {
            return ss();
        }
        return se();
    }

    function update(AdminPositionEditRequest $request, $id)
    {
        // 分离参数
        $post          = $request->validated();
        $permissionIds = $post['admin_permission_ids'];
        $departmentId  = $post['admin_department_id'];
        unset($post['admin_permission_ids']);

        // 事务开始
        $one     = new AdminPosition();
        $one->id = $id;
        $ok      = $one->getConnection()->transaction(function () use (
            $one, $post, $id,
            // 关联字段
            $permissionIds,
            $departmentId
        ) {
            // 联级状态
            // 权限默认是 USED
            AdminDepartment::used($departmentId);

            // 变更自身
            AdminPosition::query()->where('id', $id)->update($post);
            $one->permissions()->sync($permissionIds);
            return true;
        });
        if ($ok === true) {
            return ss();
        }
        return se();
    }

    function delete()
    {
        AdminPosition::clear();
        return ss();
    }
}
