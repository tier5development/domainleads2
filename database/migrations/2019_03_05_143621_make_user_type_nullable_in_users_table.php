<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeUserTypeNullableInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $sql = "ALTER TABLE `users` CHANGE `user_type` `user_type` INT(11) NULL COMMENT '1 -> level 1 user(unlock 50 leads) 2-> level 2 user(unlock 150 leads) 3-> level 3 user (unlock 500 leads) 4-> level 4 user (unlock unlimited) level 5 user -> admin'";
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        
    }
}
