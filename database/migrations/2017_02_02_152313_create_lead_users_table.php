<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadUsersTable extends Migration
{
    public function up()
    {
        Schema::create('leadusers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('registrant_email')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('registrant_email')->references('registrant_email')->on('leads')->onDelete('restrict');
        });
    }

    public function down()
    {
        
        Schema::table('leadusers', function (Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropForeign(['registrant_email']);
        });

        Schema::dropIfExists('leadusers');
    }
}
