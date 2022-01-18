<?php

use App\Models\Admin\AdminUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdminUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user', function (Blueprint $table) {
            $table->bigIncrements('id')->unique()->comment('管理员用户ID');
            $table->timestamps();

            $table->bigInteger('admin_position_id')->comment('关联管理员岗位ID');
            $table->smallInteger('status')->index()->default(_NEW)->comment('管理员用户数据状态: 1 新数据, 2 已占用, 3 异常中, 4 已停用');
            $table->smallInteger('gender')->index()->comment('性别');
            $table->smallInteger('type')->comment('用户类型')->default(1);
            $table->string('avatar', 100)->default('/avatar/admin-id-default.png')->comment('头像');
            $table->string('username', 40)->comment('用户名');
            $table->string('code', 40)->comment('用户编号');
            $table->string('id_code', 100)->nullable()->comment('身份证号');
            $table->date('birth_at')->nullable()->comment('出生日期');
            $table->string('address', 200)->nullable()->comment('居住地址');
            $table->string('phone', 11)->unique()->comment('手机号(登陆账号)');
            $table->string('email', 40)->index()->nullable()->unique('email 邮箱地址');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->comment('登陆密码');
            $table->rememberToken();
            $table->string('description', 200)->nullable()->comment('用户描述/备注');
        });

        alterTable(AdminUser::class, '管理员用户表');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user');
    }
}
