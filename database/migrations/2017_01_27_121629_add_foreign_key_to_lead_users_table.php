<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToLeadUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leadusers', function (Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('unique_hash')->references('unique_hash')->on('each_domain')->onDelete('restrict');
        });
    }
    public function down()
    {
        Schema::table('leadusers', function (Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropForeign(['unique_hash']);
        });
    }
     
}
