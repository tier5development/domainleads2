<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->index();
            $table->string('affiliate_id', 32)->index()->nullable();
            $table->string('stripe_customer_id', 30)->index()->nullable();
            $table->string('sale_id', 30)->index()->nullable();
            $table->string('stripe_plan_id', 30)->index()->nullable();
            $table->string('stripe_subscription_id', 30)->index()->nullable();
            $table->json('stripe_subscription_obj')->nullable();
            $table->json('stripe_customer_obj')->nullable();
            $table->longText('left_because')->nullable();
            $table->unsignedSmallInteger('card_updated')->unsigned()->index()->nullable();
            $table->enum('suspended', ['0', '1']);
            $table->longText('profile_image_icon');
            $table->longText('profile_image');
            $table->longText('image_path');
            $table->string('password');
            $table->integer('user_type')->index()->nullable();
            $table->tinyInteger('base_type');
            $table->string('remember_token')->nullable();
            $table->integer('membership_status')->unsigned()->index()->nullable()->default(0);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->enum('is_subscribed', ['1', '2', '3', '4', '5'])->index()->nullable()->default('2');
            $table->enum('first_visit', ['0', '1'])->default('1');
            $table->enum('email_verified', ['0', '1'])->default('1');
            $table->string('stripe_failed_invoice_id', 30)->index()->nullable();
            $table->json('stripe_failed_invoice_obj')->nullable();
            $table->enum('is_hooked', ['0', '1'])->index();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
