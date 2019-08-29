<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIvaDeveueltoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->double('total_iva_devuelto')->default(0);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->double('total_iva_devuelto')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('total_iva_devuelto');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('total_iva_devuelto');
        });
    }
}
