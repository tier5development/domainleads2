<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('prefix')->nullable();
            $table->integer('area_id')->unsigned()->index()->nullable();
            $table->string('primary_city')->nullable();
            $table->string('county')->nullable();
            $table->string('company')->nullable();
            $table->string('usage')->nullable();
            $table->timestamps();
            /** Foreign Key Definition */
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('area_codes' , function(Blueprint $table){
            $table->dropForeign(['area_id']);
        });
        Schema::dropIfExists('area_codes');
    }
}
