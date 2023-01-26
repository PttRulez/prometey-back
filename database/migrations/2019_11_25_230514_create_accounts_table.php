<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bob_id_id')->nullable();
            $table->string('nickname');
            $table->unsignedInteger('room_id');
            $table->string('disciplines')->nullable();
            $table->string('limits')->nullable();
            $table->string('limits_group')->nullable();
            $table->integer('shift_id');
            $table->unsignedInteger('affiliate_id')->nullable();
            $table->unsignedInteger('person_id')->nullable();
            $table->string('login');
            $table->string('password');
            $table->unsignedInteger('proxy_id')->nullable();
            $table->text('info')->nullable();
            $table->integer('status_id');
            $table->text('comment')->nullable();
            $table->date('creation_date');


        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('bob_id_id')->references('id')->on('bob_ids');
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('affiliate_id')->references('id')->on('affiliates');
            $table->foreign('person_id')->references('id')->on('people');
            $table->foreign('proxy_id')->references('id')->on('proxies');
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
