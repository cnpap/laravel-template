<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStopLogTable extends Migration
{
    public function up()
    {
        Schema::create('stop_log', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->timestamp('created_at');
            $table->string('subject_id');
            $table->string('description');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stop_log');
    }
}