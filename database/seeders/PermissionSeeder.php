<?php

namespace Database\Seeders;

use App\Models\Admin\AdminPermission;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolePermissions = [];
        $roles           = AdminRole::query()->select(['id'])->get();
        /** @var AdminPermission $permission */
        $permission = AdminPermission::all()->random();
        /** @var AdminRole $role */
        foreach ($roles as $role) {
            $rolePermissions[] = [
                'admin_role_id'       => $role->id,
                'admin_permission_id' => $permission->id
            ];
        }
        AdminRolePermission::query()->insert($rolePermissions);
    }
}
