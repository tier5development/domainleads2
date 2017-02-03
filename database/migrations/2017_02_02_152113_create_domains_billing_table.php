<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('domains_billing', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('billing_name');
            $table->string('billing_company');
            $table->string('billing_address');
            $table->string('billing_city');
            $table->string('billing_state');
            $table->string('billing_zip');
            $table->string('billing_country');
            $table->string('billing_email');
            $table->string('billing_phone');
            $table->string('billing_fax');
            $table->string('domain_name')->index()->unique();
            $table->timestamps();

            $table->foreign('domain_name')->references('domain_name')->on('each_domain')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::table('domains_billing' , function(Blueprint $table){
            $table->dropForeign(['domain_name']);
        });
        Schema::dropIfExists('domains_billing');
    }
}
