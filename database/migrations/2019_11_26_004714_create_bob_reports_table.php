<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBobReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bob_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('bankroll_start');
            $table->unsignedInteger('bankroll_finish');

            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bob_reports');
    }
}
