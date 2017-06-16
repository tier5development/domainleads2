<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeExpiryDateToDateNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains_info', function (Blueprint $table) {
            if(DB::select(DB::raw("SHOW KEYS FROM domains_info WHERE Key_name='domains_info_expiry_date_index'")) == null)
            {
                $table->date('expiry_date')->index()->nullable()->change();
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
