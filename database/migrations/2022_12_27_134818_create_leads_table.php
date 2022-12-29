<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('registrant_fname');
            $table->string('registrant_lname');
            $table->string('registrant_email')->unique();
            $table->string('registrant_company');
            $table->string('registrant_address');
            $table->string('registrant_city');
            $table->string('registrant_state')->nullable();
            $table->string('registrant_zip');
            $table->string('registrant_country')->nullable();
            $table->string('registrant_phone');
            $table->string('phone_validated')->nullable()->default('no');
            $table->integer('unlocked_num')->nullable();
            $table->integer('domains_count')->nullable();
            $table->string('ragistrant_fax');
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
        Schema::dropIfExists('leads');
    }
}
