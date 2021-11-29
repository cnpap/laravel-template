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
            'code'  => fnPinYin($name),
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
            $table->string('id')->unique()->comment('管理员权限ID');
            $table->string('pid')->nullable()->comment('管理员权限上级ID');
            $table->string('status', 3)->default(_NEW)->comment('管理员权限数据状态: 新数据, 已占用, 已停用, 异常中');
            $table->string('label', 40)->comment('权限标签');
            $table->string('name', 40)->comment('权限名称');
            $table->string('code', 40)->comment('权限编号');
            $table->string('description', 200)->nullable()->comment('权限描述/备注');
        });
        DB::statement("alter table `admin_permission` comment '管理员权限表'");

        Schema::create('admin_role_permission', function (Blueprint $table) {
            $table->string('admin_role_id')->comment('关联管理员角色ID');
            $table->string('admin_permission_id')->comment('关联管理员权限ID');

            $table->unique(['admin_role_id', 'admin_permission_id'], 'admin_role_permission_unique_index');
        });
        DB::statement("alter table `admin_role_permission` comment '管理员角色权限表'");

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
        Schema::dropIfExists('admin_role_permission');
        Schema::dropIfExists('admin_permission');
    }
}
