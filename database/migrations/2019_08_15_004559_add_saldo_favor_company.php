<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaldoFavorCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->float('saldo_favor_2018')->default(0);
            $table->float('saldo_favor_primer_ingreso')->default(0);
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
            $table->dropColumn('saldo_favor_2018');
            $table->dropColumn('saldo_favor_primer_ingreso');
        });
    }
}
