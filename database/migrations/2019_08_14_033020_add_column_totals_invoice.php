<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTotalsInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->double('total_serv_gravados')->default(0);
            $table->double('total_serv_exentos')->default(0);
            $table->double('total_merc_gravados')->default(0);
            $table->double('total_merc_exentas')->default(0);
            $table->double('total_gravado')->default(0);
            $table->double('total_exento')->default(0);
            $table->double('total_venta')->default(0);
            $table->double('total_descuento')->default(0);
            $table->double('total_venta_neta')->default(0);
            $table->double('total_iva')->default(0);
            $table->double('total_comprobante')->default(0);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->double('total_serv_gravados')->default(0);
            $table->double('total_serv_exentos')->default(0);
            $table->double('total_merc_gravados')->default(0);
            $table->double('total_merc_exentas')->default(0);
            $table->double('total_gravado')->default(0);
            $table->double('total_exento')->default(0);
            $table->double('total_venta')->default(0);
            $table->double('total_descuento')->default(0);
            $table->double('total_venta_neta')->default(0);
            $table->double('total_iva')->default(0);
            $table->double('total_comprobante')->default(0);
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
            $table->dropColumn('total_serv_gravados');
            $table->dropColumn('total_serv_exentos');
            $table->dropColumn('total_merc_gravados');
            $table->dropColumn('total_merc_exentas');
            $table->dropColumn('total_gravado');
            $table->dropColumn('total_exento');
            $table->dropColumn('total_venta');
            $table->dropColumn('total_descuento');
            $table->dropColumn('total_venta_neta');
            $table->dropColumn('total_iva');
            $table->dropColumn('total_comprobante');

        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('total_serv_gravados');
            $table->dropColumn('total_serv_exentos');
            $table->dropColumn('total_merc_gravados');
            $table->dropColumn('total_merc_exentas');
            $table->dropColumn('total_gravado');
            $table->dropColumn('total_exento');
            $table->dropColumn('total_venta');
            $table->dropColumn('total_descuento');
            $table->dropColumn('total_venta_neta');
            $table->dropColumn('total_iva');
            $table->dropColumn('total_comprobante');

        });
    }
}
