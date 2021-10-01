<?php

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
            $table->string('id')->unique();
            $table->timestamps();

            $table->string('admin_department_id');
            $table->string('status', 3)->default('新数据');
            $table->string('name', 40);
            $table->string('description', 200)->nullable();
        });
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
