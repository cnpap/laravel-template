<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_department', function (Blueprint $table) {
            $table->string('id')->unique()->comment('管理员部门ID');
            $table->timestamps();

            $table->string('status', 3)->default(_NEW)->comment('管理员部门数据状态: 新数据, 已占用, 已停用, 异常中');
            $table->string('name', 40)->comment('部门名称');
            $table->string('code', 40)->comment('部门编号');
            $table->string('description', 200)->nullable()->comment('部门描述/备注');
        });
        DB::statement("alter table `admin_department` comment '管理员部门表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_department');
    }
}
