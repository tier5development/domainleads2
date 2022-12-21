<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsNameserverBkpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_nameserver_bkp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_server_1');
            $table->string('name_server_2');
            $table->string('name_server_3');
            $table->string('name_server_4');
            $table->string('domain_name')->index()->unique();
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
        Schema::dropIfExists('domains_nameserver_bkp');
    }
}

