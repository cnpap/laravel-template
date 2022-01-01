<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateDevCategoryTable extends Migration
{
    public function up()
    {
        createCategoryTable('dev_category', '管理员角色权限关联表');
    }

    public function down()
    {
        Schema::dropIfExists('dev_category');
    }
}
