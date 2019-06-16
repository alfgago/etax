<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTutorialFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('users', function($table) {
            $table->dropColumn('hide_tutorial');
            $table->dropColumn('is_guest');
        });*/
        
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('hide_tutorial')->default(false);
            $table->boolean('is_guest')->default(false);
            $table->boolean('has_klap_user')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('hide_tutorial');
            $table->dropColumn('is_guest');
        });
    }
}
