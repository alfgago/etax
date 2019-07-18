<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalculatedTaxRetentionByCardColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('calculated_taxes', function (Blueprint $table) {
            $table->float('retention_by_card')->default(0);
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
            $table->dropColumn('retention_by_card');
        });
    }
}
