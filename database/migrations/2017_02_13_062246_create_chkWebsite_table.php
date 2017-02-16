<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChkWebsiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

         Schema::create('chkWebsite', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->string('domain_name');
            $table->string('status');
         

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
         Schema::dropIfExists('chkWebsite');
    }
}
