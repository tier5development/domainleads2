<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadusersBkpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leadusers_bkp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('registrant_email')->index();
            $table->string('domain_name', 100)->nullable()->index();
            $table->string('registrant_country', 25)->nullable();
            $table->string('registrant_fname')->nullable();
            $table->string('registrant_lname')->nullable();
            $table->string('registrant_company')->nullable();
            $table->string('registrant_phone', 25)->nullable();
            $table->string('number_type', 25)->nullable();
            $table->date('domains_create_date')->nullable()->index();
            $table->date('expiry_date')->nullable()->index();
            $table->softdeletes();
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
        Schema::dropIfExists('leadusers_bkp');
    }
}

