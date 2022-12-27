<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImagesToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->longText('profile_image_icon')->after('suspended')->comment('Profile icon image of an user.');
            $table->longText('profile_image')->after('profile_image_icon')->comment('Profile pic image of an user.');
            $table->longText('image_path')->after('profile_image')->comment('Profile pic url of user.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('profile_image_icon');
            $table->dropColumn('profile_image');
            $table->dropColumn('image_path');
        });
    }
}
