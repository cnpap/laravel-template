<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin\AdminPermission;

class CreateAdminPermissionTable extends Migration
{
    protected $permissions = [];

    function loopPermissions($party, $id = null)
    {
        $name                = $party['name'];
        $label               = $party['label'];
        $children            = $party['children'] ?? null;
        $permission          = [
            'id'    => uni(),
            'pid'   => $id,
            'name'  => $name,
            'label' => $label
        ];
        $this->permissions[] = $permission;
        if ($children) {
            foreach ($children as $child) {
                $this->loopPermissions($child, $permission['id']);
            }
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_permission', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('pid')->nullable();
            $table->string('status', 3)->default('已使用');
            $table->string('label', 40);
            $table->string('name', 40);
            $table->string('description', 200)->nullable();
        });

        Schema::create('admin_position_permission', function (Blueprint $table) {
            $table->string('admin_position_id');
            $table->string('admin_permission_id');

            $table->unique(['admin_position_id', 'admin_permission_id'], 'admin_position_permission_unique_index');
        });

        $materials = [
            [
                'label' => '仪表盘',
                'name'  => AdminPermission::P_DASHBOARD,
            ],
            [
                'label'    => '系统管理',
                'name'     => AdminPermission::P_SYSTEM,
                'children' => [
                    [
                        'label' => '部门管理',
                        'name'  => AdminPermission::P_ADMIN_DEPARTMENT
                    ],
                    [
                        'label' => '岗位管理',
                        'name'  => AdminPermission::P_ADMIN_POSITION
                    ],
                    [
                        'label' => '用户管理',
                        'name'  => AdminPermission::P_ADMIN_USER
                    ],
                ]
            ]
        ];
        foreach ($materials as $material) {
            $this->loopPermissions($material);
        }
        AdminPermission::query()->insert($this->permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_position_permission');
        Schema::dropIfExists('admin_permission');
    }
}
