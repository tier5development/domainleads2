<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('administrative_fname');
            $table->string('administrative_lname');
            $table->string('administrative_email');
            $table->string('administrative_company');
            $table->string('administrative_address');
            $table->string('administrative_city');
            $table->string('administrative_state')->nullable();
            $table->string('administrative_zip');
            $table->string('administrative_country')->nullable();
            $table->string('administrative_phone');
            $table->string('phone_validated')->nullable()->default('no');
            $table->integer('unlocked_num')->nullable();
            $table->integer('domains_count')->nullable();
            $table->string('ragistrant_fax');
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
        Schema::dropIfExists('leads');
    }
}
