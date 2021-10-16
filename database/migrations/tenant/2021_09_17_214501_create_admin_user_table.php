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
            $table->string('id')->unique();
            $table->timestamps();

            $table->string('admin_position_id');
            $table->string('status', 3)->default(_NEW);
            $table->string('gender', 1);
            $table->string('avatar', 100)->default('/avatar/admin-id-default.jpg');
            $table->string('username', 40);
            $table->string('phone', 11)->unique();
            $table->string('email', 40)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
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
