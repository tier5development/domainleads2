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
            $table->string('domain_name')->index()->unique();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('domain_name')->references('domain_name')->on('each_domain')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('leadusers', function (Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropForeign(['domain_name']);
        });

        Schema::dropIfExists('leadusers');
    }
}
