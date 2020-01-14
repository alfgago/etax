<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCfdpEstimadoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('calculated_taxes', function (Blueprint $table) {
            $table->double('cfdp_estimado')->default(0)->after('cfdp');; ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calculated_taxes', function (Blueprint $table) {
            $table->dropColumn('cfdp_estimado');
        });
    }
}
