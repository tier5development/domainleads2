<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsAdministrativeTable extends Migration
{
    public function up()
    {
        Schema::create('domains_administrative', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('administrative_name');
            $table->string('administrative_company');
            $table->string('administrative_address');
            $table->string('administrative_city');
            $table->string('administrative_state');
            $table->string('administrative_zip');
            $table->string('administrative_country');
            $table->string('administrative_email');
            $table->string('administrative_phone');
            $table->string('administrative_fax');
            
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
        Schema::table('domains_administrative' , function(Blueprint $table){
            $table->dropForeign(['domain_name']);
        });
        Schema::dropIfExists('domains_administrative');
    }
}
