<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXlsInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xls_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id');
            $table->integer('consecutivo');
            $table->string('codigoActividad');
            $table->string('nombreReceptor');
            $table->string('tipoIdentificacion');
            $table->string('Identificacion');
            $table->string('provincia');
            $table->string('canton');
            $table->string('distrito');
            $table->string('direccion');
            $table->enum('condicionVenta', ['01', '02', '03', '04', '05', '06', '07', '08', '99'])->default('01');
            $table->string('plazoCredito');
            $table->enum('medioPago', ['01', '02', '03', '04', '05', '99'])->default('01');
            $table->string('numeroLinea');
            $table->string('cantidad');
            $table->string('unidadMedida');
            $table->string('detalle');
            $table->string('precioUnitario');
            $table->string('montoTotal');
            $table->string('montoDescuento');
            $table->string('naturalezaDescuento');
            $table->string('subTotal');
            $table->string('codigoImpuesto');
            $table->string('codigoTarifa');
            $table->string('tarifaImpuesto');
            $table->string('montoImpuesto');
            $table->string('tipoDocumentoExoneracion');
            $table->string('numeroDocumentoExoneracion');
            $table->string('nombreInstitucionExoneracion');
            $table->string('fechaEmisionExoneracion');
            $table->string('porcentajeExoneracionExoneracion');
            $table->string('montoExoneracionExoneracion');
            $table->string('montoTotalLinea');
            $table->string('codigoMoneda');
            $table->double('tipoCambio');
            $table->double('totalServGravados');
            $table->double('totalServExentos');
            $table->double('totalMercanciasGravadas');
            $table->double('totalMercanciasExentas');
            $table->double('totalGravado');
            $table->double('totalExento');
            $table->double('totalVenta');
            $table->double('totalDescuentos');
            $table->double('totalVentaNeta');
            $table->double('totalImpuesto');
            $table->double('totalOtrosCargos');
            $table->double('totalComprobante');
            $table->integer('autorizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xls_invoices');
    }
}
