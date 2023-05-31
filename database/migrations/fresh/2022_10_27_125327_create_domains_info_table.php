<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('query_time')->nullable()->index();
            $table->date('domains_create_date')->nullable()->index();
            $table->date('domains_update_date')->nullable()->index();
            $table->date('expiry_date')->nullable()->index();
            $table->string('domain_registrar_id');
            $table->string('domain_registrar_name');
            $table->string('domain_registrar_whois');
            $table->string('domain_registrar_url');
            $table->string('domain_name', 100)->index()->unique();
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
        Schema::dropIfExists('domains_info');
    }
}

