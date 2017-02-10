<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('registrant_name');
            $table->string('registrant_email')->index()->unique();
            $table->string('registrant_company');
            $table->string('registrant_address');
            $table->string('registrant_city');
            $table->string('registrant_state');
            $table->string('registrant_zip');
            $table->string('registrant_country');
            $table->string('registrant_phone');
            $table->string('phone_validated')->nullable()->default('no');
            $table->integer('unlocked_num')->unsigned()->index()->nullable()->default(0);
            $table->string('registrant_fax');

            $table->timestamps();

            
        });
    }
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
