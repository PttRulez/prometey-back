<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calcs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_id');
            $table->string('account');
            $table->integer('hands');
            $table->integer('finish');
            $table->integer('open_tables');
            $table->integer('errors');
            $table->integer('imback');
            $table->string('allinfo');
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
        Schema::dropIfExists('calcs');
    }
}
