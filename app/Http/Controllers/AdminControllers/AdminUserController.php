<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserEditRequest;
use App\Http\Requests\Admin\AdminUserIndexRequest;
use App\Http\Requests\BulkRequest;
use App\Http\Requests\PasswordRequest;
use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminUser;
use App\Models\Admin\AdminUserOrganization;
use App\Models\Admin\AdminUserRole;
use Illuminate\Support\Facades\Hash;

/**
 * @mixin AdminUser
 */
class AdminUserController extends Controller
{
    protected $model = AdminUser::class;

    function findOrganizations($id)
    {
        $result = AdminUserOrganization::query()
            ->where('admin_user_id', $id)
            ->get()
            ->pluck('admin_organization_id')
            ->toArray();
        return result($result);
    }

    function syncOrganizations(BulkRequest $request, $id)
    {
        $userOrganizations = [];
        $ids               = $request->input('ids');
        foreach ($ids as $organizationId) {
            $userOrganizations[] = [
                'admin_user_id'         => $id,
                'admin_organization_id' => $organizationId,
            ];
        }
        $ok = (new AdminUserOrganization())->getConnection()->transaction(
            function () use (
                $userOrganizations,
                $id
            ) {
                AdminUserOrganization::query()->where('admin_user_id', $id)->delete();
                AdminUserOrganization::query()->insert($userOrganizations);
                return true;
            }
        );
        return tx($ok);
    }

    function forgotPassword(PasswordRequest $request, $id)
    {
        $password = $request->input('password');
        $password = rsaDecrypt($password);
        $secure   = passwordSecurity($password);
        if ($secure !== true) {
            return se(['message' => $secure]);
        }
        $password = Hash::make($password);
        AdminUser::query()
            ->where('id', $id)
            ->update([
                'password' => $password
            ]);
        return ss();
    }

    function roleOptions()
    {
        $options = options(AdminRole::cacheOptions());
        return result($options);
    }

    function departmentOptions()
    {
        $options = options(AdminDepartment::cacheOptions());
        return result($options);
    }

    function positionOptions()
    {
        $positions     = AdminDepartment::query()
            ->select([
                'id',
                'name'
            ])
            ->whereHas('admin_position')
            ->with(
                'admin_position:id,name,admin_department_id',
            )
            ->get();
        $treeN2Options = treeN2Options($positions, 'admin_position');
        return result($treeN2Options);
    }

    function find($id)
    {
        /** @var AdminUser $user */
        $user                   = AdminUser::query()
            ->select([
                'id',
                'status',
                'phone',
                'email',
                'gender',
                'username',
                'admin_position_id',
                'description'
            ])
            ->where('id', $id)
            ->firstOrFail();
        $user['admin_role_ids'] = $user->admin_role_ids();
        return result($user);
    }

    function list(AdminUserIndexRequest $request)
    {
        $paginator = AdminUser::indexFilter($request->validated())
            ->with('admin_position.admin_department:id,name')
            ->with('admin_position:id,name,admin_department_id')
            ->paginate(...usePage());

        return page($paginator);
    }

    function enabledList(AdminUserIndexRequest $request)
    {
        $paginator = AdminUser::indexFilter($request->validated())
            ->with('admin_position.admin_department:id,name')
            ->with('admin_position:id,name,admin_department_id')
            ->whereIn('status', ['新数据', '已占用'])
            ->paginate(...usePage());

        return page($paginator);
    }

    function create(AdminUserEditRequest $request)
    {
        // 分离参数
        $post       = $request->validated();
        $positionId = $post['admin_position_id'];
        $roleIds    = $post['admin_role_ids'];

        unset($post['admin_role_ids']);
        mergeCode($post, 'username');

        $password = $post['password'] ?? null;
        if ($password) {
            $password         = rsaDecrypt($password);
            $password         = bcrypt($password);
            $post['password'] = $password;
        }
        $user = new AdminUser($post);
        // 开始事务
        $ok = $user->getConnection()->transaction(function () use (
            $user,
            // 关联字段
            $positionId,
            $roleIds
        ) {
            // 联级状态
            AdminPosition::used($positionId);
            AdminRole::used($roleIds);

            // 自身
            $user->save();
            $id = $user->id;

            $userRoles = padKeys($id, 'admin_user_id', $roleIds, 'admin_role_id');
            AdminUserRole::query()->where('admin_user_id', $id)->delete();
            AdminUserRole::query()->insert($userRoles);

            return true;
        });
        return tx($ok);
    }

    function update(AdminUserEditRequest $request, $id)
    {
        // 分离参数
        $post       = $request->validated();
        $positionId = $post['admin_position_id'];
        $roleIds    = $post['admin_role_ids'];

        unset($post['admin_role_ids']);
        mergeCode($post, 'username');

        $password = $post['password'] ?? null;
        if ($password) {
            $password         = rsaDecrypt($password);
            $password         = bcrypt($password);
            $post['password'] = $password;
        }
        // 开始事务
        $user = new AdminUser();
        $ok   = $user->getConnection()->transaction(function () use (
            $id, $post,
            // 关联字段
            $positionId,
            $roleIds
        ) {
            // 联级状态
            AdminPosition::used($positionId);
            AdminRole::used($roleIds);

            $userRoles = padKeys($id, 'admin_user_id', $roleIds, 'admin_role_id');
            AdminUserRole::query()->where('admin_user_id', $id)->delete();
            AdminUserRole::query()->insert($userRoles);

            // 自身
            AdminUser::query()->where('id', $id)->update($post);
            return true;
        });
        return tx($ok);
    }
}
