<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin\AdminDepartment;

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
            $table->bigIncrements('id')->unique()->comment('管理员部门ID');
            $table->timestamps();
            $table->smallInteger('status')->default(_NEW)->comment('管理员部门数据状态: 1 新数据, 2 已占用, 3 异常中, 4 已停用');
            $table->string('name', 40)->comment('部门名称');
            $table->string('code', 40)->comment('部门编号');
            $table->string('description', 200)->nullable()->comment('部门描述/备注');
        });
        AdminDepartment::clearCacheOptions();

        $defaultName = '默认部门';
        $defaultCode = fnPinYin($defaultName);
        AdminDepartment::query()
            ->create([
                'status' => _USED,
                'name'   => $defaultName,
                'code'   => $defaultCode
            ]);
        $externName = '外部部门';
        $externCode = fnPinYin($externName);
        AdminDepartment::query()
            ->create([
                'status' => _USED,
                'name'   => $externName,
                'code'   => $externCode,
            ]);

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
