<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExpiryDateColToLeadusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leadusers', function(Blueprint $table) {
            $table->date('expiry_date')->nullable()->index()->after('domains_create_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leadusers', function(Blueprint $table) {
            $table->dropIndex(['expiry_date']);
            $table->dropColumn('expiry_date');
        });
    }
}
