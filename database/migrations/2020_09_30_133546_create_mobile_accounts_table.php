<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bobid')->nullable();
            $table->string('game_type');
            $table->unsignedInteger('computer_id');
            $table->string('player_id');
            $table->string('nickname');
            $table->string('mobile_club_id');
            $table->unsignedInteger('proxy_id');
            $table->string('login');
            $table->string('password');
            $table->integer('roll_start');
            $table->integer('roll_end')->nullable();
            $table->boolean('banned')->default(false);
            $table->date('created_date');
            $table->date('banned_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_accounts');
    }
}

