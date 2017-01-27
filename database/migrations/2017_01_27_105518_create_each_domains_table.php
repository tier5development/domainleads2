<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEachDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('each_domain', function (Blueprint $table) {

        $table->bigIncrements('id');

        $table->string('domain_name')->index()->nullable();

        $table->string('domain_ext')->nullable();

        $table->string('unique_hash')->index()->unique()->nullable();

        $table->integer('unlocked_num')->unsigned()->nullable()->default(0)->index();

        $table->timestamps();

       });
    }

    
    public function down()
    {
        Schema::dropIfExists('each_domain');
    }
}
