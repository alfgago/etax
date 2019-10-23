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
            $table->bigInteger('company_id')->default(0);
            $table->string('tipoDocumento')->nullable();
            $table->string('consecutivo')->nullable();
            $table->string('fechaEmision')->nullable();
            $table->string('fechaVencimiento')->nullable();
            $table->string('codigoActividad')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('nombreEmisor')->nullable();
            $table->string('tipoIdentificacionEmisor')->nullable();
            $table->string('identificacionEmisor')->nullable();
            $table->string('correoEmisor')->nullable();
            $table->string('provinciaEmisor')->nullable();
            $table->string('cantonEmisor')->nullable();
            $table->string('distritoEmisor')->nullable();
            $table->string('direccionEmisor')->nullable();
            $table->string('nombreReceptor')->nullable();
            $table->string('tipoIdentificacionReceptor')->nullable();
            $table->string('identificacionReceptor')->nullable();
            $table->string('correoReceptor')->nullable();
            $table->string('provinciaReceptor')->nullable();
            $table->string('cantonReceptor')->nullable();
            $table->string('distritoReceptor')->nullable();
            $table->string('direccionReceptor')->nullable();
            $table->enum('condicionVenta', ['01', '02', '03', '04', '05', '06', '07', '08', '99'])->default('01');
            $table->string('plazoCredito')->nullable();
            $table->enum('medioPago', ['01', '02', '03', '04', '05', '99'])->default('01');
            $table->string('tipoLinea')->default(1);
            $table->string('numeroLinea')->default(0);
            $table->double('cantidad')->default(0);
            $table->string('unidadMedida')->nullable();
            $table->string('detalle')->nullable();
            $table->double('precioUnitario')->default(0);
            $table->double('montoTotal')->default(0);
            $table->double('montoDescuento')->default(0);
            $table->double('naturalezaDescuento')->default(0);
            $table->double('subTotal')->default(0);
            $table->string('codigoImpuesto')->nullable();
            $table->string('codigoTarifa')->nullable();
            $table->string('tarifaImpuesto')->nullable();
            $table->double('montoImpuesto')->default(0);
            $table->double('exento')->default(0);
            $table->string('tipoDocumentoExoneracion')->nullable();
            $table->string('numeroDocumentoExoneracion')->nullable();
            $table->string('nombreInstitucionExoneracion')->nullable();
            $table->string('fechaEmisionExoneracion')->nullable();
            $table->string('porcentajeExoneracionExoneracion')->nullable();
            $table->string('montoExoneracionExoneracion')->nullable();
            $table->double('montoTotalLinea')->default(0);
            $table->string('tipoCargo')->nullable();
            $table->string('identidadTercero')->nullable();
            $table->string('nombreTercero')->nullable();
            $table->string('detalleCargo')->nullable();
            $table->string('porcentajeCargo')->nullable();
            $table->double('montoCargo')->default(0);
            $table->string('codigoMoneda')->nullable();
            $table->double('tipoCambio')->default(0);
            $table->double('totalServGravados')->default(0);
            $table->double('totalServExentos')->default(0);
            $table->double('totalServExonerados')->default(0);
            $table->double('totalMercanciasGravadas')->default(0);
            $table->double('totalMercanciasExentas')->default(0);
            $table->double('totalMercanciasExonerada')->default(0);
            $table->double('totalGravado')->default(0);
            $table->double('totalExento')->default(0);
            $table->double('totalExonerado')->default(0);
            $table->double('totalVenta')->default(0);
            $table->double('totalDescuentos')->default(0);
            $table->double('totalVentaNeta')->default(0);
            $table->double('totalImpuesto')->default(0);
            $table->double('totalOtrosCargos')->default(0);
            $table->double('totalComprobante')->default(0);
            $table->integer('autorizado')->default(1);
            $table->timestamps();
            $table->softDeletes();
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
