<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditColumnXML extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('xml_haciendas', 'xml_message'))
        {
            Schema::table('xml_haciendas', function (Blueprint $table)
            {
                $table->dropColumn('xml_message');
            });
        }

        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->string('xml_message')->nullable();
        });

        if (Schema::hasColumn('xml_haciendas', 'xml_note'))
        {
            Schema::table('xml_haciendas', function (Blueprint $table)
            {
                $table->dropColumn('xml_note');
            });
        }

        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->string('xml_note')->nullable();
        });

        if (Schema::hasColumn('xml_haciendas', 'xml_reception'))
        {
            Schema::table('xml_haciendas', function (Blueprint $table)
            {
                $table->dropColumn('xml_reception');
            });
        }

        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->string('xml_reception')->nullable();
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
            $table->dropColumn('xml_messages');
        });
        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->dropColumn('xml_note');
        });
        Schema::table('xml_haciendas', function (Blueprint $table) {
            $table->dropColumn('xml_reception');
        });
    }
}
