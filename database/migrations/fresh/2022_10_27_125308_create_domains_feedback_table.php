<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain_name')->unique()->nullable();
            $table->string('curl_error')->nullable();
            $table->string('content')->nullable();
            $table->integer('checked')->default(0)->nullable();
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
        Schema::dropIfExists('domains_feedback');
    }
}

