<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            $table->string('provider');
          
            //Tipo de documento de referencia. 01 Factura electrónica, 02 Nota de débito electrónica, 03 nota de crédito electrónica, 04 Tiquete electrónico, 05 Nota de despacho, 06 Contrato, 07 Procedimiento, 08 Comprobante emitido en contigencia, 99 Otros
            $table->string('document_type');
          
            $table->string('invoice_key');
          
            $table->integer('reference_number');
            $table->string('document_number');
          
            $table->double('subtotal');
            $table->double('iva_amount');
            $table->double('total');
            $table->string('currency');
            $table->double('currency_rate');

            //Condiciones de la venta: 01 Contado, 02 Crédito, 03 Consignación, 04 Apartado, 05 Arrendamiento con opción de compra, 06 Arrendamiento en función financiera, 99 Otros
            $table->enum('sale_condition', ['01', '02', '03', '04', '05', '06', '99'])->default('01');
          
            //Plazo del crédito, es obligatorio cuando la venta del product o prestación del servicio sea a crédito
            $table->string('credit_time')->nullable();
          
            //Corresponde al medio de pago empleado: 01 Efectivo, 2 Tarjeta, 03 Cheque, 04 Transferencia - depósito bancario, 05 - Recaudado por terceros, 99 Otros
            $table->enum('payment_type', ['01', '02', '03', '04', '05', '99'])->default('01');
          
            $table->string('buy_order')->nullable();
            $table->string('send_emails');
            $table->text('description')->nullable();
            $table->string('hacienda_status');
            $table->string('payment_status');
            $table->string('payment_receipt')->nullable();
            $table->timestamp('generated_date');
            $table->timestamp('due_date');
          
            $table->boolean('is_exempt')->default(false);
            $table->boolean('is_void')->default(false);
          
            //Estado de aceptación de la factura: 01 Pediente, 02 Aceptada, 03 Rechazada, 04 Anulada, 05 Parcial
            $table->enum('status', ['01', '02', '03', '04', '05'])->default('01');
          
            $table->string('generation_method');
            $table->string('other_reference')->nullable();
          
            $table->timestamps();
        });
      
        Schema::create('bill_items', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('item_number');
            $table->string('code');
            $table->string('name');
            $table->string('product_type');
            $table->integer('measure_unit');
            $table->integer('item_count');
            $table->double('unit_price');
            $table->double('subtotal');
            $table->double('total');
            $table->double('discount_percentage')->default(0);
            $table->double('discount_reason')->nullable();
            $table->double('iva_type');
            $table->double('iva_percentage');
            $table->boolean('is_exempt')->default(false);
          
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
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
    }
}
