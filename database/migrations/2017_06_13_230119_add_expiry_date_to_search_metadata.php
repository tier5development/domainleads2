<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiryDateToSearchMetadata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_metadata',function (Blueprint $table){
            $table->date('expiry_date')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_metadata' , function(Blueprint $table){
            $table->dropIndex(['expiry_date']);
            $table->dropColumn('expiry_date');
        });
    }
}
