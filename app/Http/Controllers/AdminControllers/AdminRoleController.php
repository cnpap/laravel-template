<?php

namespace App\Http\Controllers\AdminControllers;

use App\Cache\PermissionCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRoleEditRequest;
use App\Http\Requests\Admin\AdminRoleIndexRequest;
use App\Http\Requests\Admin\AdminRolePermissionRequest;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRolePermissionName;

class AdminRoleController extends Controller
{
    function permissionTable()
    {
        $manager   = new PermissionCache();
        $tableData = $manager->permissionTable();
        return result($tableData);
    }

    function syncPermissionNames(AdminRolePermissionRequest $request, $id)
    {
        $ok = (new AdminRole())->getConnection()->transaction(
            function () use ($request, $id) {
                AdminRolePermissionName::query()->where('admin_role_id', $id)->delete();

                $names               = $request->input('names');
                $rolePermissionNames = [];
                foreach ($names as $name) {
                    $rolePermissionNames[] = [
                        'admin_role_id'   => $id,
                        'permission_name' => $name
                    ];
                }
                AdminRolePermissionName::query()->insert($rolePermissionNames);

                return true;
            }
        );
        return tx($ok);
    }

    function find($id)
    {
        $one = AdminRole::query()
            ->with('permission_name:permission_name')
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

                mergeCode($post);

                AdminRole::query()->where('id', $id)->update($post);
                $role     = new AdminRole($post);
                $role->id = $id;

                return true;
            }
        );
        return tx($ok);
    }

    function create(AdminRoleEditRequest $request)
    {
        $ok = (new AdminRole())->getConnection()->transaction(
            function () use ($request) {
                $post            = $request->validated();

                mergeCode($post);

                $role     = new AdminRole($post);
                $role->id = uni();

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
