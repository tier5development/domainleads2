<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexingAllSearchTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DELETE from `valid_phone` where number_type != 'Landline' 
            and number_type != 'Cell Number'");
        Schema::table('leads', function (Blueprint $table) {
            if(DB::select(DB::raw("SHOW KEYS FROM leads WHERE Key_name='leads_registrant_country_index'")) == null)
            {
                $table->string('registrant_country',25)->index()->nullable()->change();
            }
            if(DB::select(DB::raw("SHOW KEYS FROM leads WHERE Key_name='leads_registrant_state_index'")) == null)
            {
                $table->string('registrant_state',25)->index()->nullable()->change();
            }
        });
        Schema::table('domains_info', function (Blueprint $table){
            if(DB::select(DB::raw("SHOW KEYS FROM domains_info WHERE Key_name='domains_info_domains_create_date_index'")) == null)
            {
                $table->date('domains_create_date')->index()->nullable()->change();
            }
            if(DB::select(DB::raw("SHOW KEYS FROM domains_info WHERE Key_name='domains_info_domains_update_date_index'")) == null)
            {
                $table->date('domains_update_date')->index()->nullable()->change();
            }
        });
        Schema::table('valid_phone', function (Blueprint $table) {
           if(DB::select(DB::raw("SHOW KEYS FROM valid_phone WHERE Key_name='valid_phone_number_type_index'")) == null)
            {
                $table->string('number_type',16)->index()->nullable()->change();
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
    
    }
}
