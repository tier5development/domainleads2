<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentOptionsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function(Blueprint $table) {
            $table->tinyInteger('base_type')->after('user_type')->comment('Same as user_type field, stores the subscription state comming from the affiliate platform');
            $table->string('stripe_customer_id', 30)->index()->nullable()->after('affiliate_id')->comment('Stripe Customer Id');
            $table->string('sale_id', 30)->index()->nullable()->after('stripe_customer_id')->comment('Sales id of a customer as received from affiliates platform.');
            $table->string('stripe_plan_id', 30)->index()->nullable()->after('sale_id')->comment('Stripe Plan Id');
            $table->string('stripe_subscription_id', 30)->index()->nullable()->after('stripe_plan_id')->comment('Stripe Subscription Id');
            $table->json('stripe_subscription_obj')->nullable()->after('stripe_subscription_id')->comment('Stripe Subscription Obj');
            $table->json('stripe_customer_obj')->nullable()->after('stripe_subscription_obj')->comment('Stripe Customer Obj');
            $table->longText('left_because')->nullable()->after('stripe_customer_obj')->comment('Reason why user chooses to downgrade.');
            $table->unsignedSmallInteger('card_updated')->index()->nullable()->after('left_because')->comment('Card updated information of user.');
            $table->unsignedTinyInteger('is_subscribed')->default(0)->index()->after('card_updated')->comment('0 -> subscription failed(passed due, cancelled, or unpaid equivalent in stripe), 1-> trailing equivalent in stripe, 2-> active equivalent in stripe');
            $table->enum('first_visit', [0,1])->after('is_subscribed')->default(1)->comment('0 -> first visit, 1-> multivisit');
            $table->enum('email_verified', [0,1])->after('first_visit')->default(1)->comment('0 -> email not verified, 1-> email verified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('base_type');
            $table->dropIndex(['stripe_customer_id']);
            $table->dropColumn('stripe_customer_id');
            $table->dropIndex(['sale_id']);
            $table->dropColumn('sale_id');
            $table->dropIndex(['stripe_plan_id']);
            $table->dropColumn('stripe_plan_id');
            $table->dropIndex(['stripe_subscription_id']);
            $table->dropColumn('stripe_subscription_id');
            $table->dropIndex(['card_updated']);
            $table->dropColumn('card_updated');
            $table->dropColumn('stripe_subscription_obj');
            $table->dropColumn('stripe_customer_obj');
            $table->dropColumn('left_because');
            $table->dropIndex(['is_subscribed']);
            $table->dropColumn('is_subscribed');
            $table->dropColumn('first_visit');
            $table->dropColumn('email_verified');
        });
    }
}
