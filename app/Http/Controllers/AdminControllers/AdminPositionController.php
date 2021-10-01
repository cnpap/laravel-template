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
        /** @var AdminPosition $position */
        $position                         = AdminPosition::query()
            ->select([
                'id',
                'status',
                'name',
                'description',
                'admin_department_id',
            ])
            ->findOrFail($id);
        $position['admin_permission_ids'] = $position->permissions()->get()->modelKeys();
        return result($position);
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
        $position     = new AdminPosition($post);
        $position->id = uni();
        $ok           = $position->getConnection()->transaction(function () use (
            $position, $post,
            // 关联字段
            $permissionIds,
            $departmentId
        ) {
            // 联级数据状态变更到已使用
            // 权限默认是 USED
            AdminDepartment::used($departmentId);

            // 变更自身
            $position->permissions()->sync($permissionIds);
            $position->save();
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
        $position     = new AdminPosition();
        $position->id = $id;
        $ok           = $position->getConnection()->transaction(function () use (
            $position, $post, $id,
            // 关联字段
            $permissionIds,
            $departmentId
        ) {
            // 联级状态
            // 权限默认是 USED
            AdminDepartment::used($departmentId);

            // 变更自身
            AdminPosition::query()->where('id', $id)->update($post);
            $position->permissions()->sync($permissionIds);
            return true;
        });
        if ($ok === true) {
            return ss();
        }
        return se();
    }

    function delete($id)
    {
        AdminPosition::clear($id);
        return ss();
    }
}
