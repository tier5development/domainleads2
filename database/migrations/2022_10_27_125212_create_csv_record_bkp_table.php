<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvRecordBkpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_record_bkp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_name')->unique()->nullable();
            $table->timestamps();
            $table->integer('leads_inserted')->unsigned()->index()->default(0)->nullable();
            $table->integer('domains_inserted')->unsigned()->index()->default(0)->nullable();
            $table->integer('query_time')->unsigned()->index()->default(0)->nullable(); //in seconds
            $table->integer('status')->unsigned()->index()->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_record_bkp');
    }
}

