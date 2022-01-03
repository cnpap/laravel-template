<?php

use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
                'id'                  => '_position1',
                'status'              => _USED,
                'name'                => '默认岗位',
                'code'                => 'mrgw',
                'description'         => '默认岗位',
                'admin_department_id' => '_department1'
            ]);
        AdminPosition::query()
            ->create([
                'id'                  => '_position2',
                'status'              => _USED,
                'name'                => '外部岗位',
                'code'                => 'wbgw',
                'description'         => '外部岗位',
                'admin_department_id' => '_department2'
            ]);

        $username                 = '真实名称z';
        $super                    = new AdminUser();
        $super->admin_position_id = '_position1';
        $super->id                = '_super_manager';
        $super->gender            = _MAN;
        $super->status            = _USED;
        $super->username          = $username;
        $super->code              = fnPinYin($username);
        $super->phone             = '19977775555';
        $super->email             = 'sia-fl@outlook.com';
        $super->password          = bcrypt('123456');
        $super->save();

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
