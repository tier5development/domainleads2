<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetadataSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('search_metadata', function (Blueprint $table) {
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
            $table->longText('leads')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_metadata');
    }
}
