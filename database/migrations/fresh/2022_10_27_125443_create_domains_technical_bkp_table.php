<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTechnicalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_technical', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('technical_name');
            $table->string('technical_company');
            $table->string('technical_address');
            $table->string('technical_city');
            $table->string('technical_state');
            $table->string('technical_zip');
            $table->string('technical_country');
            $table->string('technical_email');
            $table->string('technical_phone');
            $table->string('technical_fax');
            $table->string('domain_name')->index()->unique();
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
        Schema::dropIfExists('domains_technical');
    }
}

