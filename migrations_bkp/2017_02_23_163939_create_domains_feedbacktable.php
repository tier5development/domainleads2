<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsFeedbacktable extends Migration
{
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
    public function down()
    {
        Schema::dropIfExists('domains_feedback');
    }
}
