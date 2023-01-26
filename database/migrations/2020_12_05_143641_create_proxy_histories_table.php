<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxyHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_histories', function (Blueprint $table) {

            $table->unsignedInteger('proxy_id');
            $table->unsignedInteger('account_id');
            $table->timestamps();
            $table->primary(['proxy_id', 'account_id']);
            $table->foreign('proxy_id')->references('id')->on('proxies');
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
        Schema::dropIfExists('proxy_histories');
    }
}
