<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBobIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bob_ids', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bob_id');
            $table->unsignedInteger('network_id');
            $table->string('disciplines');
            $table->string('limits');

            $table->foreign('network_id')->references('id')->on('networks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bob_ids');
    }
}
