<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReduceIndexSizesInLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // reduce registrant_email column in leads table
        $data = DB::select(DB::raw("select column_name, data_type, character_maximum_length from information_schema.columns where table_name = 'leads'"));
        // $flag_re = 0;
        // $flag_zip = 0;
        foreach ($data as $key => $value) 
        {
            if($value->column_name=='registrant_email' && $value->character_maximum_length>110) 
            { 
                Schema::table('leads', function (Blueprint $table){
                    $table->string('registrant_email',110)->change();
                });
                //break;
            }
            if($value->column_name=='registrant_zip' && $value->character_maximum_length>15) 
            { 
                Schema::table('leads', function (Blueprint $table){
                    $table->string('registrant_zip',15)->index()->change();
                });
                //break;
            }
        }

        $flag = 0;
        $data = DB::select(DB::raw('Show INDEX FROM leads'));
        foreach ($data as $key => $value) 
        {
            if($value->Key_name == "leads_registrant_zip_index")
            {
                $flag = 1;
            }
        }
        if($flag == 0)
        {
            Schema::table('leads', function (Blueprint $table){
                $table->string('registrant_zip',15)->index()->change();
            });
        }

        // reduce registrant_email column in each_domain table
        $data = DB::select(DB::raw("select column_name, data_type, character_maximum_length from information_schema.columns where table_name = 'each_domain'"));
        foreach ($data as $key => $value) 
        {
            if($value->column_name=='registrant_email' && $value->character_maximum_length>110) 
            { 
                Schema::table('each_domain', function (Blueprint $table){
                    $table->string('registrant_email',110)->change();
                });
                break;
            }
        }

        // reduce registrant_email column in valid_phone table
        $data = DB::select(DB::raw("select column_name, data_type, character_maximum_length from information_schema.columns where table_name = 'valid_phone'"));
        foreach ($data as $key => $value) 
        {
            if($value->column_name=='registrant_email' && $value->character_maximum_length>110) 
            {
                Schema::table('valid_phone', function (Blueprint $table){
                    $table->string('registrant_email',110)->change();
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
