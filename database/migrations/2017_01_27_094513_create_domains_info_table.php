<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->integer('user_id');
            //$table->string('domain_name');
            $table->string('query_time');
            $table->string('create_date');
            $table->string('update_date');
            $table->string('expiry_date');
            $table->integer('domain_registrar_id')->unsigned()->index();
            $table->string('domain_registrar_name');
            $table->string('domain_registrar_whois');
            $table->string('domain_registrar_url');
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
        Schema::dropIfExists('domains_info');
    }
}
