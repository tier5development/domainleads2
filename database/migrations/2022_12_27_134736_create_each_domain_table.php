<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEachDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('each_domain', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain_name');
            $table->string('domain_ext');
            $table->integer('unlocked_num');
            $table->string('ragistrant_email');
            $table->timestamps();
            $table->tinyInteger('marked')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('each_domain');
    }
}
