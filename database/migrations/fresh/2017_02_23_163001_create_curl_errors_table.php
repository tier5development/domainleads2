<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurlErrorsTable extends Migration
{
    public function up()
    {
        Schema::create('curl_errors', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->string('curl_error')->nullable()->unique();
            $table->string('err_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('curl_errors');
    }
}
