<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnsToLeadusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leadusers', function (Blueprint $table) {
            $table->string('domain_name', 100)->nullable()->index()->after('registrant_email');
            $table->string('registrant_country', 25)->nullable()->after('domain_name');
            $table->string('registrant_fname', 255)->nullable()->after('registrant_country');
            $table->string('registrant_lname', 255)->nullable()->after('registrant_fname');
            $table->string('registrant_company', 255)->nullable()->after('registrant_lname');
            $table->string('registrant_phone', 25)->nullable()->after('registrant_company');
            $table->string('number_type', 25)->nullable()->after('registrant_phone');
            $table->date('domains_create_date')->nullable()->after('number_type');
            $table->softdeletes()->after('domains_create_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leadusers', function (Blueprint $table) {
            $table->dropIndex(['domain_name']);
            $table->dropColumn('domain_name');
            $table->dropColumn('registrant_country');
            $table->dropColumn('registrant_fname');
            $table->dropColumn('registrant_lname');
            $table->dropColumn('registrant_company');
            $table->dropColumn('registrant_phone');
            $table->dropColumn('number_type');
            $table->dropColumn('domains_create_date');
            $table->dropColumn('deleted_at');
        });
    }
}
