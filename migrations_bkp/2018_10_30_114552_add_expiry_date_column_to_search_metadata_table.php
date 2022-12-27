<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiryDateColumnToSearchMetadataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_metadata', function(Blueprint $table) {
            $table->date('expiry_date2')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_metadata', function(Blueprint $table) {
            $table->dropIndex(['expiry_date2']);
            $table->dropColumn('expiry_date2');
        });
    }
}
