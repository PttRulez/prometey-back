<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalAndWonToBobReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bob_reports', function (Blueprint $table) {
            $table->integer('win');
            $table->integer('total');
            $table->unsignedInteger('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bob_reports', function (Blueprint $table) {
            $table->dropColumn('win');
            $table->dropColumn('total');
            $table->dropColumn('currency_id');
        });
    }
}
