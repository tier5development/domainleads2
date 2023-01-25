<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidPhoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valid_phone', function(Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number')->unique()->default(null);
            $table->string('validation_status')->default(null);
            $table->string('state')->default(null);
            $table->string('major_city')->default(null);
            $table->string('primary_city')->default(null);
            $table->string('county')->default(null);
            $table->string('carrier_name')->default(null);
            $table->string('number_type')->default(null);
            $table->string('registrant_email')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('valid_phone');
    }
}
