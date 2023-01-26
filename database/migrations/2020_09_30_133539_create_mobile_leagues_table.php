<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileLeaguesTable extends Migration
{
    public function up()
    {
        Schema::create('mobile_leagues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('room_id');
            $table->text('info')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mobile_leagues');
    }
}
