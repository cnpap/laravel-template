<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDevCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('dev_category', function (Blueprint $table) {
            $table->string('id', 16)->unique()->comment('分类ID');
            $table->string('pid', 16)->nullable()->comment('上级分类');
            $table->timestamps();

            $table->string('status', 3)->default(_NEW)->comment('分类数据状态: 新数据, 已占用, 已停用, 异常中');
            $table->string('name', 40)->comment('分类名称');
            $table->string('code', 40)->comment('分类编号');
            $table->string('description', 200)->nullable()->comment('分类描述/备注');
        });

        DB::statement("alter table `admin_role_permission_name` comment '管理员角色权限关联表'");
    }

    public function down()
    {
        Schema::dropIfExists('dev_category');
    }
}
