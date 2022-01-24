<?php

use App\Models\Admin\AdminOrganization;
use App\Models\Admin\AdminUserOrganization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminOrganizationTable extends Migration
{
    public function up()
    {
        if (config('app.organization')) {
            Schema::create('admin_organization', function (Blueprint $table) {
                $table->bigIncrements('id')->unique()->comment('管理员组织ID');
                $table->timestamps();

                $table->smallInteger('status')->index()->default(_NEW)->comment('管理员组织数据状态: 1 新数据, 2 已占用, 3 异常中, 4 已停用');
                $table->string('name', 40)->unique()->comment('组织名称');
                $table->string('code', 40)->comment('组织编号');
                $table->string('description', 200)->nullable()->comment('组织描述/备注');
            });

            alterTable(AdminOrganization::class, '管理员组织');

            Schema::create('admin_user_organization', function (Blueprint $table) {
                $table->bigInteger('admin_user_id')->index()->comment('关联管理员用户ID');
                $table->bigInteger('admin_organization_id')->index()->comment('关联管理员组织ID');
                $table->unique(['admin_user_id', 'admin_organization_id'], 'admin_user_organization_unique_index');
            });

            alterTable(AdminUserOrganization::class, '管理员用户组织关联表');
        }
    }

    public function down()
    {
        Schema::dropIfExists('admin_organization');
    }
}