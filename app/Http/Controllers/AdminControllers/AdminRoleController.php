<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRoleEditRequest;
use App\Http\Requests\Admin\AdminRoleIndexRequest;
use App\Models\Admin\AdminPermission;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminRole;

class AdminRoleController extends Controller
{
    function find($id)
    {
        $one = AdminRole::query()
            ->with('permissions:id,pid,name,code')
            ->where('id', $id)
            ->first();
        return result($one);
    }

    function list(AdminRoleIndexRequest $request)
    {
        $paginator = AdminRole::filter($request->validated())
            ->paginate(...usePage());

        return page($paginator);
    }

    function update(AdminRoleEditRequest $request, $id)
    {
        $ok = (new AdminRole())->getConnection()->transaction(
            function () use ($request, $id) {
                $post                = $request->validated();
                $post['description'] = $post['description'] ?? null;
                $permissionIds       = $post['admin_permission_ids'];
                unset($post['admin_permission_ids']);

                mergeCode($post, 'name');

                AdminRole::query()->where('id', $id)->update($post);
                $role     = new AdminRole($post);
                $role->id = $id;
                $role->permissions()->sync($permissionIds);

                return true;
            }
        );
        return tx($ok);
    }

    function create(AdminRoleEditRequest $request)
    {
        $ok = (new AdminRole())->getConnection()->transaction(
            function () use ($request) {
                $post          = $request->validated();
                $permissionIds = $post['admin_permission_ids'];
                unset($post['admin_permission_ids']);

                $role     = new AdminRole($post);
                $role->id = uni();
                $role->permissions()->sync($permissionIds);

                $role->save();

                return true;
            }
        );
        return tx($ok);
    }

    function position()
    {
        $positions = AdminPosition::query()->get();
        return result($positions);
    }

    function permissions()
    {
        $permissions = AdminPermission::query()->get();
        return result($permissions);
    }

    function status()
    {
        AdminRole::status();
        return ss();
    }

    function delete()
    {
        AdminRole::clear();
        return ss();
    }
}
