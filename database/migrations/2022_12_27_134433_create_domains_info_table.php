<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->date('query_time')->nullable();
            $table->date('domain_created_date')->nullable();
            $table->date('domain_updated_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('domain_registrar_id');
            $table->string('domain_registrar_name');
            $table->string('domain_registrar_whois');
            $table->string('domain_registrar_url');
            $table->string('domain_name')->unique();
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
