<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->bigInteger('id')->unique();
            $table->timestamps();

            $table->bigInteger('admin_position_id');
            $table->smallInteger('status')->default(OK);
            $table->smallInteger('gender');
            $table->string('avatar', 100)->default('/default.jpg');
            $table->string('username', 40);
            $table->string('phone', 11)->unique();
            $table->string('email', 40)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
        });
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
