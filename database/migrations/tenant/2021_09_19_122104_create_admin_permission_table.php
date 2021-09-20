<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin\AdminPermission;

class CreateAdminPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_permission', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->bigInteger('pid')->default(0);
            $table->smallInteger('status')->default(USED);
            $table->string('label', 40);
            $table->string('name', 40);
            $table->string('description', 200)->nullable();
        });

        Schema::create('admin_position_permission', function (Blueprint $table) {
            $table->bigInteger('admin_position_id');
            $table->bigInteger('admin_permission_id');

            $table->unique(['admin_position_id', 'admin_permission_id'], 'admin_position_permission_unique_index');
        });

        $materials   = [
            AdminPermission::P_DASHBOARD        => '仪表盘',
            AdminPermission::P_SYSTEM           => '系统管理',
            AdminPermission::P_ADMIN_USER       => '用户管理',
            AdminPermission::P_ADMIN_DEPARTMENT => '部门管理',
            AdminPermission::P_ADMIN_POSITION   => '岗位管理',
        ];
        $permissions = [];
        foreach ($materials as $name => $label) {
            $permissions[] = [
                'id'    => uni(),
                'name'  => $name,
                'label' => $label
            ];
        }
        AdminPermission::query()->insert($permissions);
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
