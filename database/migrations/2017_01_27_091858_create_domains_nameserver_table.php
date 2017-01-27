<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsNameserverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_nameserver', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name_server_1');
            $table->string('name_server_2');
            $table->string('name_server_3');
            $table->string('name_server_4');

            $table->string('unique_hash')->index()->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains_nameserver');
    }
}
