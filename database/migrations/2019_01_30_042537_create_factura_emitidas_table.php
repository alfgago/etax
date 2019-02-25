<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturaEmitidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura_emitidas', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('cliente_id')->nullable();
          
            //Tipo de documento de referencia. 01 Factura electrónica, 02 Nota de débito electrónica, 03 nota de crédito electrónica, 04 Tiquete electrónico, 05 Nota de despacho, 06 Contrato, 07 Procedimiento, 08 Comprobante emitido en contigencia, 99 Otros
            $table->string('tipo_documento');
          
            $table->string('clave_factura');
          
            $table->integer('numero_referencia');
            $table->string('numero_documento');
          
            $table->double('subtotal');
            $table->double('monto_iva');
            $table->double('total');
            $table->string('moneda');
            $table->double('tipo_cambio');

            //Condiciones de la venta: 01 Contado, 02 Crédito, 03 Consignación, 04 Apartado, 05 Arrendamiento con opción de compra, 06 Arrendamiento en función financiera, 99 Otros
            $table->string('condicion_venta');
          
            //Plazo del crédito, es obligatorio cuando la venta del producto o prestación del servicio sea a crédito
            $table->string('plazo_credito')->nullable();
          
            //Corresponde al medio de pago empleado: 01 Efectivo, 2 Tarjeta, 03 Cheque, 04 Transferencia - depósito bancario, 05 - Recaudado por terceros, 99 Otros
            $table->string('medio_pago');
          
            $table->string('orden_compra')->nullable();
            $table->string('correos_envio');
            $table->text('notas')->nullable();
            $table->string('estado_hacienda');
            $table->string('estado_pago');
            $table->string('comprobante_pago')->nullable();
            $table->timestamp('fecha_generada');
            $table->timestamp('fecha_vencimiento');
          
            $table->boolean('factura_exenta')->default(false);
            $table->boolean('factura_anulada')->default(false);
          
            //Estos se usan en caso de no haber un cliente registrado.
            $table->string('tipo_identificacion_cliente');
            $table->string('identificacion_cliente');
            $table->string('nombre_cliente');
            $table->string('cliente_exento');
          
            $table->string('metodo_generacion');
            $table->string('referencia')->nullable();
          
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
        Schema::dropIfExists('factura_emitidas');
    }
}
