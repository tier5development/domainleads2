<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSearchMetadataBkpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_metadata_bkp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain_name',70)->nullable()->index();
            $table->string('domain_ext',40)->nullable()->index();
            $table->string('registrant_country',25)->nullable()->index();
            $table->string('registrant_state',75)->nullable()->index();

            $table->date('domains_create_date1')->nullable()->index();
            $table->date('domains_create_date2')->nullable()->index();
            $table->unsignedInteger('domains_count')->nullable()->index();
            $table->string('number_type',30)->nullable()->index();

            $table->string('sortby',40)->nullable()->index();
            $table->string('domains_count_operator',1)->nullable()->index();
            $table->string('leads_unlocked_operator',1)->nullable()->index();
            $table->unsignedInteger('unlocked_num')->nullable()->index();

            $table->unsignedInteger('search_priority')->nullable()->index();
            $table->unsignedInteger('totalLeads')->nullable()->index();
            $table->unsignedInteger('totalDomains')->nullable()->index();
            //$table->dateTime('last_searched')->nullable()->index();

            $table->unsignedTinyInteger('compression_level')->nullable()->index();
            $table->timestamps();
            $table->string('registrant_zip',15)->nullable()->index();
            $table->float('query_time')->nullable()->index();
            $table->date('expiry_date')->index()->nullable();
            $table->date('expiry_date2')->nullable()->index();
        });

        DB::statement('ALTER TABLE `search_metadata_bkp` ADD `leads` LONGBLOB NULL AFTER `updated_at`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_metadata_bkp');
    }
}

