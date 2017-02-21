<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCSVTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('csv_record', function (Blueprint $table) {
            $table->integer('leads_inserted')->unsigned()->index()->default(0)->nullable();
            $table->integer('domains_inserted')->unsigned()->index()->default(0)->nullable();
            $table->integer('query_time')->unsigned()->index()->default(0)->nullable(); //in seconds
            $table->integer('status')->unsigned()->index()->default(0)->nullable();

            //status-->0-->stacked..
            //status-->1-->processing
            //status-->2-->processing completed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('csv_record',function(Blueprint $table){
            $table->dropColumn('leads_inserted');
            $table->dropColumn('domains_inserted');
            $table->dropColumn('query_time');
            $table->dropColumn('status');
        });
    }
}
