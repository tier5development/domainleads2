<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidatePhonenumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('validatephone', function (Blueprint $table) {
            $table->increments('id');
           
            $table->string('user_id');
            $table->string('http_code');
            $table->string('phone_number');
            $table->string('state');
            $table->string('major_city');
            $table->string('primary_city');
            $table->string('county');
            $table->string('carrier_name');
            $table->string('number_type');
                  
           // $table->string('domain_id');
            
            
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
        Schema::drop('validatephone');
    }
}
