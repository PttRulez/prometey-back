<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileClubsTable extends Migration
{

    public function up()
    {
        Schema::create('mobile_clubs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('club_id');
            $table->string('agent_id')->nullable();
            $table->unsignedInteger('mobile_league_id');
            $table->integer('activity_status');
            $table->string('chip_rate');
            $table->string('limitations')->nullable();
            $table->text('info')->nullable();
        });
    }


    public function down()
    {
        Schema::dropIfExists('mobile_clubs');
    }
}
