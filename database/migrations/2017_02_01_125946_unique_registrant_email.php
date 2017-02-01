<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UniqueRegistrantEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unique_registrant_email', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->index()->unique();
            $table->string('domain_name')->index()->unique();
            $table->integer('doamin_count')->unsigned()->index()->default(0);
            $table->text('domains')->nullable();
            $table->timestamps();

            
            $table->foreign('domain_name')->references('domain_name')->on('each_domain')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unique_registrant_email', function (Blueprint $table){
            $table->dropForeign(['domain_name']);
        });

        Schema::dropIfExists('unique_registrant_email');
    }
}
