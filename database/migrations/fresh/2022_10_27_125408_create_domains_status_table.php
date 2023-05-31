<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->string('domain_status_1');
            $table->string('domain_status_2');
            $table->string('domain_status_3');
            $table->string('domain_status_4');
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
        Schema::dropIfExists('domains_status');
    }
}

