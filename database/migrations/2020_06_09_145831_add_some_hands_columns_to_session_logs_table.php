<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeHandsColumnsToSessionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_logs', function (Blueprint $table) {
            $table->integer('hands_in_session');
            $table->integer('hands_in_request');
            $table->unsignedInteger('hands_summed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session_logs', function (Blueprint $table) {
            $table->dropColumn('hands_in_session');
            $table->dropColumn('hands_in_request');
            $table->dropColumn('hands_summed');
        });
    }
}
