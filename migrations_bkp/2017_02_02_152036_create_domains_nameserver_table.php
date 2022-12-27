<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsNameserverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_nameserver', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name_server_1');
            $table->string('name_server_2');
            $table->string('name_server_3');
            $table->string('name_server_4');
            $table->string('domain_name')->index()->unique();
            $table->timestamps();

            //$table->foreign('domain_name')->references('domain_name')->on('each_domain')->onDelete('cascade');
        });
    }
    public function down()
    {
        // Schema::table('domains_nameserver' , function(Blueprint $table){
        //     $table->dropForeign(['domain_name']);
        // });
        Schema::dropIfExists('domains_nameserver');
    }
}
