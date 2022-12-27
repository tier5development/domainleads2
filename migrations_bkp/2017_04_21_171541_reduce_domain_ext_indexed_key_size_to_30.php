<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReduceDomainExtIndexedKeySizeTo30 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // reduce domain_name column in each_domain table
        $data = DB::select(DB::raw("select column_name, data_type, character_maximum_length from information_schema.columns where table_name = 'each_domain'"));
        foreach ($data as $key => $value) 
        {
            if ($value->column_name=='domain_ext' && $value->character_maximum_length>30) 
            { 
                Schema::table('each_domain', function (Blueprint $table){
                    $table->string('domain_ext',30)->change();
                });
                break;
            }
        }
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
