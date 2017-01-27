<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('domains_status', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('domain_status_1');
            $table->string('domain_status_2');
            $table->string('domain_status_3');
            $table->string('domain_status_4');

            $table->string('unique_hash')->index()->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains_status');
    }
}
