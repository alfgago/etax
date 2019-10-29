<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyColumIntegraciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('integraciones', function (Blueprint $table) {
            $table->string('secret_key')->nulleable();

        });    
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('integraciones', function (Blueprint $table) {
            $table->dropColumn('secret_key');
        });
    }
}
