<?php

use App\Models\Admin\AdminDepartment;
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
            $table->bigIncrements('id')->unique()->comment('管理员岗位ID');
            $table->timestamps();

            $table->smallInteger('status')->index()->default(_NEW)->comment('管理员岗位数据状态: 1 新数据, 2 已占用, 3 异常中, 4 已停用');
            $table->bigInteger('admin_department_id')->index()->comment('关联管理员部门ID');
            $table->string('name', 40)->comment('岗位名称');
            $table->string('code', 40)->comment('岗位编号');
            $table->string('description', 200)->nullable()->comment('岗位描述/备注');
        });

        alterTable(AdminPosition::class, '管理员岗位表');

        /** @var AdminDepartment $defaultDepartment */
        $defaultDepartment                    = AdminDepartment::query()
            ->where('name', '默认部门')
            ->first();
        $defaultPositionName                  = '默认岗位';
        $defaultPositionCode                  = fnPinYin($defaultPositionName);
        $defaultPosition                      = new AdminPosition();
        $defaultPosition->status              = _USED;
        $defaultPosition->name                = $defaultPositionName;
        $defaultPosition->code                = $defaultPositionCode;
        $defaultPosition->description         = $defaultPositionName;
        $defaultPosition->admin_department_id = $defaultDepartment->id;
        $defaultPosition->save();

        /** @var AdminDepartment $externDepartment */
        $externDepartment                    = AdminDepartment::query()
            ->where('name', '外部部门')
            ->first();
        $externPositionName                  = '外部岗位';
        $externPositionCode                  = fnPinYin($externPositionName);
        $externPosition                      = new AdminPosition();
        $externPosition->status              = _USED;
        $externPosition->name                = $externPositionName;
        $externPosition->code                = $externPositionCode;
        $externPosition->description         = $externPositionName;
        $externPosition->admin_department_id = $externDepartment->id;
        $externPosition->save();

        $username                 = '真实名称z';
        $userCode                 = fnPinYin($username);
        $super                    = new AdminUser();
        $super->admin_position_id = $defaultPosition->id;
        $super->gender            = _MAN;
        $super->status            = _USED;
        $super->username          = $username;
        $super->code              = $userCode;
        $super->phone             = '19977775555';
        $super->email             = 'sia-fl@outlook.com';
        $super->password          = bcrypt('123456');
        $super->save();
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
