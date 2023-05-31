<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ValidPhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valid_phone' , function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string("phone_number")->index()->unique();
            $table->string("validation_status");
            $table->string("state");
            $table->string("major_city");
            $table->string("primary_city");
            $table->string("county");
            $table->string("carrier_name");
            $table->string("number_type");
            $table->string('registrant_email')->index();
            $table->timestamps();

            //$table->foreign('registrant_email')->references('registrant_email')->on('leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('valid_phone', function (Blueprint $table){
        //     $table->dropForeign(['registrant_email']);
        // });
        
        Schema::dropIfExists('valid_phone');
    }
}
