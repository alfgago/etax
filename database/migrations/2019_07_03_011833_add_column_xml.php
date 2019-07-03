<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnXml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->bigInteger('xml_reception')->nullable();
        });

        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->bigInteger('xml_note')->nullable();
        });

        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->bigInteger('xml_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->dropColumn('xml_reception');
        });

        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->dropColumn('xml_note');
        });
        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->dropColumn('xml_message');
        });
    }
}
