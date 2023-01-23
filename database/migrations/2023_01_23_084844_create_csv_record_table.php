<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsvRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_record', function(Blueprint $table) {
            $table->increments('id');
            $table->string('file_name')->nullable();
            $table->integer('leads_inserted')->default(0);
            $table->integer('domains_inserted')->default(0);
            $table->integer('query_time')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('csv_record');
    }
}
