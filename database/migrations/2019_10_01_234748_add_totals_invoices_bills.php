<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalsInvoicesBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->double('total_serv_exonerados')->default(0);
            $table->double('total_merc_exonerados')->default(0);
            $table->double('total_exonerados')->default(0);

        });

        Schema::table('bills', function (Blueprint $table) {
            $table->double('total_serv_exonerados')->default(0);
            $table->double('total_merc_exonerados')->default(0);
            $table->double('total_exonerados')->default(0);
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
            $table->dropColumn('total_serv_exonerados');
            $table->dropColumn('total_merc_exonerados');
            $table->dropColumn('total_exonerados');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('total_serv_exonerados');
            $table->dropColumn('total_merc_exonerados');
            $table->dropColumn('total_exonerados');
        });
    }
}
