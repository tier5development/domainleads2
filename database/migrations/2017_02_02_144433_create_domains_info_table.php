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
            $table->string('query_time');
            $table->string('domains_create_date');
            $table->string('domains_update_date');
            $table->string('expiry_date');
            $table->string('domain_registrar_id');
            $table->string('domain_registrar_name');
            $table->string('domain_registrar_whois');
            $table->string('domain_registrar_url');
            $table->string('domain_name')->index()->unique();
            
            $table->timestamps();

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
