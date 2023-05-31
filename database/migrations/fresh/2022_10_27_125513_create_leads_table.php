<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->string('registrant_fname');
            $table->string('registrant_lname');
            $table->string('registrant_email')->index()->unique();
            $table->string('registrant_company');
            $table->string('registrant_address');
            $table->string('registrant_city');
            $table->string('registrant_state')->nullable()->index();
            $table->string('registrant_zip')->index();
            $table->string('registrant_country')->nullable()->index();
            $table->string('registrant_phone');
            $table->string('phone_validated')->nullable()->default('no');
            $table->integer('unlocked_num')->unsigned()->index()->nullable()->default(0);
            $table->integer('domains_count')->unsigned()->index()->nullable()->default(0);
            $table->string('registrant_fax');
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

