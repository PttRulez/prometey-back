<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashouts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_id')->default(1);
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('status_id');
            $table->date('ordered_date')->nullable();
            $table->date('left_balance_date')->nullable();
            $table->date('returned_balance_date')->nullable();
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
        Schema::dropIfExists('cashouts');
    }
}
