<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsInfoTable extends Migration
{
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
            $table->string('domain_registrar_id');
            $table->string('domain_registrar_name');
            $table->string('domain_registrar_whois');
            $table->string('domain_registrar_url');
            $table->string('domain_name')->index()->unique();

            $table->foreign('domain_name')->references('domain_name')->on('each_domain')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('domains_info' , function(Blueprint $table){
            $table->dropForeign(['domain_name']);
        });
        
        Schema::dropIfExists('domains_info');
    }
}
