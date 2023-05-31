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
        $table->string('domain_name')->index()->unique();
        $table->string('domain_ext')->index();
        $table->integer('unlocked_num')->unsigned()->nullable()->default(0)->index();
        $table->string('registrant_email')->index();
        $table->timestamps();
        $table->tinyInteger('marker')->index()->nullable();

        // $table->foreign('registrant_email')->references('registrant_email')->on('leads')->onDelete('cascade');
       });
    }

    
    public function down()
    {
        // Schema::table('each_domain', function (Blueprint $table){
        //     $table->dropForeign(['registrant_email']);
        // });
        Schema::dropIfExists('each_domain');
    }
}
