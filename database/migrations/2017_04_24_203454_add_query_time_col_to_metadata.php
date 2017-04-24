<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQueryTimeColToMetadata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_metadata', function (Blueprint $table) {
            $table->float('query_time')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_metadata', function (Blueprint $table) {
            $table->dropIndex(['query_time']);
            $table->dropColumn('query_time');
        });
    }
}
