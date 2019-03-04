<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Carbon\Carbon;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id')->nullable();
          
            //Tipo de documento de referencia. 01 Factura electrónica, 02 Nota de débito electrónica, 03 nota de crédito electrónica, 04 Tiquete electrónico, 05 Nota de despacho, 06 Contrato, 07 Procedimiento, 08 Comprobante emitido en contigencia, 99 Otros
            $table->string('document_type');
          
            $table->string('document_key');
          
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
          
            //Estos se usan en caso de no haber un cliente registrado.
            $table->string('client_id_type');
            $table->string('client_id_temp');
            $table->string('client_name');
            $table->string('client_is_exempt')->default(false);
          
            $table->string('generation_method');
            $table->string('other_reference')->nullable();
          
            $table->timestamps();
        });
      
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('invoice_id');
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
        //$this->demoData();
    }
  
    public function demoData() {
        
        $company = \App\Company::first();
        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\es_ES\Person($faker));
      
        for($i = 0; $i < 1000; $i++) {
            $invoice = new \App\Invoice();
            $invoice->company_id = $company->id;
            //Datos generales y para Hacienda
            $invoice->document_type = "01";
            $invoice->document_key = "50601021900310270242900100001010000000162174804809";
            $invoice->reference_number = $company->last_invoice_ref_number + 1;
            $numero_doc = ((int)$company->last_document) + 1;
            $invoice->document_number = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);

            $invoice->sale_condition = $faker->randomElement(array('01','02','03','04','05','99'));
            $invoice->payment_type = $faker->randomElement(array('01','02','03','04','05','99'));
            $invoice->credit_time = 0;
            $invoice->buy_order = $faker->lexify('???');
            $invoice->other_reference = '';
            $invoice->hacienda_status = "01";
            $invoice->payment_status = "01";
            $invoice->payment_receipt = "VOUCHER-123451234512345";
            $invoice->generation_method = "M";

            //Datos de cliente
            $invoice->client_name = $faker->name;
            $invoice->client_id_type = 'F';
            $invoice->client_id = $faker->numerify('1-####-####');
            $invoice->client_is_exempt = false;
            $invoice->send_emails = $faker->companyEmail;

            //Datos de factura
            $invoice->description = $faker->sentence;
            $invoice->currency = 'CRC';
            $invoice->currency_rate = '1';
            /*
            $invoice->subtotal = $request->subtotal;
            $invoice->total = $request->total;
            $invoice->iva_amount = $request->iva_amount;
            */

            $invoice->save();

            $sumTotal = 0;
            $sumSubtotal = 0;
            $sumIva = 0;
            for($j = 0; $j < $faker->numberBetween(1, 4); $j++) {
              $item_number = $j;
              $lexify = $faker->lexify('???');
              $code = 'C-' . $lexify;
              $name = 'P-' . $lexify;
              
              $productCategory = \App\ProductCategory::find($faker->numberBetween(1, 8));
              $product_type = $productCategory->id;
              $iva_type = $productCategory->invoice_iva_code;
              $iva_percentage = (double)\App\Variables::getTipoRepercutidoIVAPorc( $iva_type );
              
              $measure_unit = '1';
              $item_count = $faker->numberBetween(1, 3);
              $unit_price = $faker->numberBetween(100, 25000);
              $subtotal = $item_count*$unit_price;
              
              $iva_amount = $subtotal * $iva_percentage / 100;
              $total = $subtotal + $iva_amount;
              $discount_percentage = '0';
              $discount_reason = '';
              $is_exempt = false;

              $invoice->addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );
              
              $sumTotal = $sumTotal + $total;
              $sumSubtotal = $sumSubtotal + $subtotal;
              $sumIva = $sumIva + $iva_amount;
            }
          
            $invoice->total = $sumTotal;
            $invoice->subtotal = $sumSubtotal;
            $invoice->iva_amount = $sumIva;

            //Fechas
            if( $i < 500 ){
              $fecha = $faker->dateTimeBetween($startDate = '2018-09-01 02:00:00', $endDate = '2018-12-31 02:00:00');
            }else if( $i >= 500 && $i < 750){
              $fecha = $faker->dateTimeBetween($startDate = '2019-01-01 02:00:00', $endDate = '2019-01-31 02:00:00');
            }else{
              $fecha = $faker->dateTimeBetween($startDate = '2019-02-01 02:00:00', $endDate = '2019-02-28 02:00:00');
            }
          
            $invoice->generated_date = Carbon::createFromFormat('d/m/Y g:i A', $fecha->format('d/m/Y g:i A') );
            $invoice->due_date = Carbon::createFromFormat('d/m/Y g:i A', $fecha->format('d/m/Y g:i A') )->addDays(15);
            $invoice->save();
          
            $company->last_invoice_ref_number = $invoice->reference_number;
            $company->last_document = $invoice->document_number;
            $company->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
}
