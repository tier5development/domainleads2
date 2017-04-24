<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegistrantZipColumnToMetadataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_metadata', function (Blueprint $table) {
            $table->string('registrant_zip',15)->nullable()->index();
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
            $table->dropIndex(['registrant_zip']);
            $table->dropColumn('registrant_zip');
        });
    }
}
