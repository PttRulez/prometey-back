<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHandsPlayedToBobReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bob_reports', function (Blueprint $table) {
            $table->unsignedInteger('hands_played')->default(0);
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
            $table->dropColumn('hands_played');
        });
    }
}
