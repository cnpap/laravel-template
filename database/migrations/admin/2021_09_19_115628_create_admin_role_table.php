<?php

use App\Models\Admin\AdminRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminRoleTable extends Migration
{
    public function up()
    {
        Schema::create('admin_role', function (Blueprint $table) {
            $table->string('id', 16)->unique()->comment('管理员角色ID');
            $table->timestamps();

            $table->string('status', 3)->comment('管理员角色数据状态: 新数据, 已占用, 已停用, 异常中')->default(_NEW);
            $table->string('name', 40)->unique()->comment('角色名称');
            $table->string('code', 40)->unique()->comment('角色编号');
            $table->string('description', 200)->nullable()->comment('角色描述');
        });
        DB::statement("alter table `admin_role` comment '管理员角色表'");

        Schema::create('admin_user_role', function (Blueprint $table) {
            $table->string('admin_user_id', 16)->comment('关联管理员用户ID');
            $table->string('admin_role_id', 16)->comment('关联管理员角色ID');
            $table->unique(['admin_user_id', 'admin_role_id'], 'admin_user_role_unique_index');
        });
        DB::statement("alter table `admin_user_role` comment '管理员角色权限关联表'");

        Schema::create('admin_role_permission_name', function (Blueprint $table) {
            $table->string('admin_role_id', 16)->comment('关联管理员角色ID');
            $table->string('permission_name')->comment('权限 name 标识');
            $table->unique(['admin_role_id', 'permission_name'], 'admin_role_permission_name_unique_index');
        });
        DB::statement("alter table `admin_role_permission_name` comment '管理员角色权限关联表'");

        AdminRole::clearCacheOptions();
    }

    public function down()
    {
        Schema::dropIfExists('admin_role');
        Schema::dropIfExists('admin_user_role');
        Schema::dropIfExists('admin_role_permission_name');
    }
}
