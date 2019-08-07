<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBookToCalculatedTaxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('calculated_taxes', function (Blueprint $table) {
            $table->double('sum_iva_sin_aplicar')->default(0);
            $table->longText('book_data')->nullable();
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
            $table->dropColumn('sum_iva_sin_aplicar');
            $table->dropColumn('book_data');
        });
    }
}
