<?php

namespace App\Http\Controllers\AdminControllers;

use App\Cache\PermissionCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRoleEditRequest;
use App\Http\Requests\Admin\AdminRoleIndexRequest;
use App\Http\Requests\Admin\AdminRolePermissionRequest;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRolePermissionName;

class AdminRoleController extends Controller
{
    protected $model = AdminRole::class;

    function findPermissionNames($id)
    {
        $rolePermissionNames = AdminRolePermissionName::query()
            ->where('admin_role_id', $id)
            ->get()
            ->pluck('permission_name')
            ->toArray();
        $manager             = new PermissionCache();
        $result              = $manager->itemTable($rolePermissionNames);
        return result($result);
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
        $paginator = AdminRole::indexFilter($request->validated())
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
                $post = $request->validated();

                mergeCode($post);

                $role     = new AdminRole($post);
                $role->save();

                return true;
            }
        );
        return tx($ok);
    }
}
