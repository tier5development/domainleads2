<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_status_1');
            $table->string('name_status_2');
            $table->string('name_status_3');
            $table->string('name_status_4');
            $table->string('domain_name')->unique();
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
        Schema::dropIfExists('domains_status');
    }
}
