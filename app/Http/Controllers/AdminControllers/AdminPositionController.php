<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPositionEditRequest;
use App\Http\Requests\Admin\AdminPositionIndexRequest;
use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPosition;

class AdminPositionController extends Controller
{
    function find($id)
    {
        $position = AdminPosition::query()->findOrFail($id);
        return ss($position);
    }

    function list(AdminPositionIndexRequest $request)
    {
        $result = AdminPosition::page($request);
        return ss($result);
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
            $position->adminPermissions()->sync($permissionIds);
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
            $position->adminPermissions()->sync($permissionIds);
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
