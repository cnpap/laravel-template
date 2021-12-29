<?php

use App\Models\Admin\AdminPosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_position', function (Blueprint $table) {
            $table->string('id')->unique()->comment('管理员岗位ID');
            $table->timestamps();

            $table->string('admin_department_id')->comment('关联管理员部门ID');
            $table->string('status', 3)->default(_NEW)->comment('管理员岗位数据状态: 新数据, 已占用, 已停用, 异常中');
            $table->string('name', 40)->comment('岗位名称');
            $table->string('code', 40)->comment('岗位编号');
            $table->string('description', 200)->nullable()->comment('岗位描述/备注');
        });

        AdminPosition::query()
            ->create([
                'id'                  => 'external',
                'status'              => _USED,
                'name'                => '外部岗位',
                'code'                => 'external',
                'description'         => '外部',
                'admin_department_id' => 'external'
            ]);
        DB::statement("alter table `admin_position` comment '管理员岗位表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_position');
    }
}
