<?php

use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRolePermissionName;
use App\Models\Admin\AdminUserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminRoleTable extends Migration
{
    public function up()
    {
        Schema::create('admin_role', function (Blueprint $table) {
            $table->bigIncrements('id')->unique()->comment('管理员角色ID');
            $table->timestamps();

            $table->smallInteger('status')->index()->default(_NEW)->comment('管理员角色数据状态: 1 新数据, 2 已占用, 3 异常中, 4 已停用');
            $table->string('name', 40)->unique()->comment('角色名称');
            $table->string('code', 40)->unique()->comment('角色编号');
            $table->string('description', 200)->nullable()->comment('角色描述');
        });

        alterTable(AdminRole::class, '管理员角色表');

        Schema::create('admin_user_role', function (Blueprint $table) {
            $table->bigInteger('admin_user_id')->index()->comment('关联管理员用户ID');
            $table->bigInteger('admin_role_id')->index()->comment('关联管理员角色ID');
            $table->unique(['admin_user_id', 'admin_role_id'], 'admin_user_role_unique_index');
        });

        alterTable(AdminUserRole::class, '管理员角色权限关联表');

        Schema::create('admin_role_permission_name', function (Blueprint $table) {
            $table->bigInteger('admin_role_id')->index()->comment('关联管理员角色ID');
            $table->string('permission_name')->index()->comment('权限 name 标识');
            $table->unique(['admin_role_id', 'permission_name'], 'admin_role_permission_name_unique_index');
        });

        alterTable(AdminRolePermissionName::class, '管理员角色权限关联表');

        AdminRole::clearCacheOptions();
    }

    public function down()
    {
        Schema::dropIfExists('admin_role');
        Schema::dropIfExists('admin_user_role');
        Schema::dropIfExists('admin_role_permission_name');
    }
}
