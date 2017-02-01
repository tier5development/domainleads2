<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
   public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('registrant_name');
            $table->string('registrant_email');
            $table->string('registrant_company');
            $table->string('registrant_address');
            $table->string('registrant_city');
            $table->string('registrant_state');
            $table->string('registrant_zip');
            $table->string('registrant_country');
            $table->string('registrant_phone');
            $table->string('phone_type')->nullable();
            $table->string('registrant_fax');

            $table->string('domain_name')->index()->unique();

            $table->foreign('domain_name')->references('domain_name')->on('each_domain')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::table('leads' , function(Blueprint $table){
            $table->dropForeign(['domain_name']);
        });

        Schema::dropIfExists('leads');
    }
}
