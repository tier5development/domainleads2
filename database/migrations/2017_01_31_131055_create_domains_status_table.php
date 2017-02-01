<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsStatusTable extends Migration
{
   public function up()
    {
         Schema::create('domains_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain_status_1');
            $table->string('domain_status_2');
            $table->string('domain_status_3');
            $table->string('domain_status_4');

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
        Schema::table('domains_status' , function(Blueprint $table){
            $table->dropForeign(['domain_name']);
        });
        Schema::dropIfExists('domains_status');
    }
}
