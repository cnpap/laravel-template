<?php

namespace Database\Seeders;

use App\Models\Admin\AdminPermission;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminPositionPermission;
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
        $positionPermissions = [];
        $positions           = AdminPosition::query()->select(['id'])->get();
        /** @var AdminPermission $permission */
        $permission = AdminPermission::all()->random();
        /** @var AdminPosition $position */
        foreach ($positions as $position) {
            $positionPermissions[] = [
                'admin_position_id'   => $position->id,
                'admin_permission_id' => $permission->id
            ];
        }
        AdminPositionPermission::query()->insert($positionPermissions);
    }
}
