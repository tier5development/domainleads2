<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAffiliateIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE `users` CHANGE `affiliate_id` `affiliate_id` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT 'affiliate id comming in from other application';";
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
