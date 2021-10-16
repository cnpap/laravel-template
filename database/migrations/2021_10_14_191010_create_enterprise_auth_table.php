<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterpriseAuthTable extends Migration
{
    public function up()
    {
        Schema::create('enterprise_auth', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->timestamps();

            $table->smallInteger('status')->comment('1: 允许受邀, 2: 停止受邀, 3: 已入驻')->default(1);
            $table->smallInteger('audit_status')->comment('1: 等待审核, 2: 审核成功, 3: 审核失败')->default(1);
            $table->string('audit_message')->comment('审核信息')->nullable();
            $table->string('zc_zb')->comment('注册资本')->nullable();
            $table->text('jy_fw')->comment('经营范围')->nullable();
            $table->string('fr_card_code', 40)->comment('法人身份证号')->nullable();
            $table->string('fr_name', 40)->comment('法人名称')->nullable();
            $table->string('qy_name', 200)->comment('企业名称')->nullable();
            $table->string('qy_site', 200)->comment('企业地址')->nullable();
            $table->string('qy_type', 100)->comment('企业类型')->nullable();
            $table->string('zj_code')->comment('证件编号')->nullable();
            $table->string('xy_code')->comment('社会信用代码')->nullable();
            $table->date('yx_sj')->comment('证件有效期')->nullable();
            $table->date('cl_sj')->comment('成立时间')->nullable();
            $table->date('fr_card_qf_sj')->comment('法人身份证创建时间')->nullable();
            $table->date('fr_card_yx_sj')->comment('法人身份证有效时间')->nullable();
            $table->string('contact')->comment('公司业务联系方式')->nullable();

            $table->string('gl_name')->comment('管理员名称')->nullable();
            $table->string('gl_phone')->comment('管理员手机号')->nullable();
            $table->string('gl_email')->comment('管理员邮箱')->nullable();

            $table->string('fr_img_1')->comment('身份证正面照片')->nullable();
            $table->string('fr_img_2')->comment('身份证反面照片')->nullable();
            $table->string('qy_img_1')->comment('公司门面照片')->nullable();
            $table->string('qy_img_2')->comment('公司环境照片')->nullable();
            $table->string('zj_img')->comment('证件照片')->nullable();

            $table->string('bank_region_code')->comment('地区编号对应的数据库id')->nullable();
            $table->string('bank_name')->comment('银行名称')->nullable();
            $table->string('bank_code')->comment('银行卡号')->nullable();
            $table->string('bank_store_name')->comment('开户行名称')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enterprise_auth');
    }
}