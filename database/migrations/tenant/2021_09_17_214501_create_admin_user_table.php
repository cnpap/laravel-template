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
            $table->smallInteger('status')->default(1);
            $table->smallInteger('sex');
            $table->string('avatar', 100)->default('/default.jpg');
            $table->string('nick_name', 40);
            $table->string('real_name', 40);
            $table->string('phone', 11)->unique();
            $table->string('email', 40)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
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
