<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocketMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create("socket_meta", function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leads_unlocked');
            $table->unsignedBigInteger('total_domains');
            $table->unsignedBigInteger('total_users');
            $table->unsignedBigInteger('leads_added_last_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists("socket_meta");
    }
}
