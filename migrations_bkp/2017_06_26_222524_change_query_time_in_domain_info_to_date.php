<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeQueryTimeInDomainInfoToDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains_info', function (Blueprint $table) {
            if(DB::select(DB::raw("SHOW KEYS FROM domains_info WHERE Key_name='domains_info_query_time_index'")) == null)
            {
                $table->date('query_time')->index()->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
