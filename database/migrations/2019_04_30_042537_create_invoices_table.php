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
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
          
            //Tipo de documento de referencia. 01 Factura electrónica, 02 Nota de débito electrónica, 03 nota de crédito electrónica, 04 Tiquete electrónico, 05 Nota de despacho, 06 Contrato, 07 Procedimiento, 08 Comprobante emitido en contigencia, 99 Otros
            $table->string('document_type')->nullable();
          
            $table->string('document_key')->nullable();
          
            $table->integer('reference_number')->default(0);
            $table->string('document_number')->nullable();
          
            $table->double('subtotal')->default(0);
            $table->double('iva_amount')->default(0);
            $table->double('total')->default(0);
            $table->string('currency')->default('01');
            $table->double('currency_rate')->default(1);

            //Condiciones de la venta: 01 Contado, 02 Crédito, 03 Consignación, 04 Apartado, 05 Arrendamiento con opción de compra, 06 Arrendamiento en función financiera, 99 Otros
            $table->enum('sale_condition', ['01', '02', '03', '04', '05', '06', '99'])->default('01');
          
            //Plazo del crédito, es obligatorio cuando la venta del product o prestación del servicio sea a crédito
            $table->string('credit_time')->nullable();
          
            //Corresponde al medio de pago empleado: 01 Efectivo, 2 Tarjeta, 03 Cheque, 04 Transferencia - depósito bancario, 05 - Recaudado por terceros, 99 Otros
            $table->enum('payment_type', ['01', '02', '03', '04', '05', '99'])->default('01');
            $table->double('retention_percent')->default(0);
          
            $table->string('buy_order')->nullable();
            $table->string('send_emails')->nullable();
            $table->text('description')->nullable();
            $table->string('hacienda_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->timestamp('generated_date')->nullable();
            $table->timestamp('due_date')->nullable();
          
            $table->boolean('is_void')->default(false);
            $table->boolean('is_totales')->default(false);
            //Cuando se ingresa via XML o Email, tienen que validar que el código eTax esté correcto.
            $table->boolean('is_code_validated')->default(true);
            
            //Estado de aceptación de la factura: 01 Pendiente, 02 Aceptada, 03 Rechazada, 04 Anulada, 05 Parcial
            $table->enum('status', ['01', '02', '03', '04', '05'])->default('01');
          
            $table->string('generation_method')->nullable();
            $table->string('other_reference')->nullable();
            
            $table->unsignedBigInteger('other_document')->default(0);
          
            $table->integer('month')->default(1);
            $table->integer('year')->default(2019);
            $table->index(['year', 'month']);
          
            $table->timestamps();
        });
      
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('invoice_id')->default(0);
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            $table->unsignedBigInteger('company_id')->default(0);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('item_number')->default(0);
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('product_type')->nullable();
            $table->string('measure_unit')->nullable();
            $table->integer('item_count')->default(0);
            $table->double('unit_price')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('iva_amount')->default(0);
            $table->double('total')->default(0);
            $table->string('discount_type')->nullable();
            $table->double('discount')->default(0);
            $table->string('discount_reason')->nullable();
            $table->string('iva_type')->nullable();
            $table->double('iva_percentage')->default(0);
            $table->boolean('is_exempt')->default(false);
            $table->boolean('is_identificacion_especifica')->default(false);
          
            $table->integer('month')->default(1);
            $table->integer('year')->default(2019);
            $table->index(['year', 'month']);
          
            $table->timestamps();
        });
        
        if ( !app()->environment('production') ) {
            $this->demoData();
        }
    }
  
  
    public function demoData() {
        
        $company = \App\Company::first();
        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\es_ES\Person($faker));
      
        for($i = 0; $i < 3000; $i++) {
            
            \DB::transaction( function () use($faker, $company, $i) {
                
                //Fechas
                if( $i < 1000 ){
                  $fecha = $faker->dateTimeBetween($startDate = '2018-09-01 02:00:00', $endDate = '2018-12-31 02:00:00');
                }else if( $i >= 1000 && $i < 1750){
                  $fecha = $faker->dateTimeBetween($startDate = '2019-02-01 02:00:00', $endDate = '2019-02-28 02:00:00');
                }else if( $i >= 1750 && $i < 2250){
                  $fecha = $faker->dateTimeBetween($startDate = '2019-03-01 02:00:00', $endDate = '2019-03-28 02:00:00');
                }else{
                  $fecha = $faker->dateTimeBetween($startDate = '2019-04-01 02:00:00', $endDate = '2019-04-28 02:00:00');
                }
                
                $generated_date = Carbon::createFromFormat('d/m/Y g:i A', $fecha->format('d/m/Y g:i A') );
                
                $invoice = new \App\Invoice();
                $invoice->company_id = $company->id;
                //Datos generales y para Hacienda
                $invoice->document_type = "01";
                $invoice->document_key = "";
                $invoice->reference_number = $company->last_invoice_ref_number + 1;
                $numero_doc = ((int)$company->last_document) + 1;
                $invoice->document_number = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
    
                $invoice->sale_condition = $faker->randomElement(array('01','02'));
                $invoice->payment_type = $faker->randomElement(array('01','02','03','04','05','99'));
                $invoice->credit_time = 0;
                $invoice->buy_order = $faker->lexify('???');
                $invoice->other_reference = '';
                $invoice->hacienda_status = "01";
                $invoice->payment_status = "01";
                $invoice->payment_receipt = "VOUCHER-123451234512345";
                $invoice->generation_method = "M";
    
                //Datos de cliente
                /*$invoice->client_name = $faker->name;
                $invoice->client_id_type = 'F';
                $invoice->client_id = $faker->numerify('1-####-####');
                $invoice->client_is_exempt = false;*/
                $invoice->client_id = $faker->numberBetween(1,50);
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
    
                $invoice->year = $generated_date->year;
                $invoice->month = $generated_date->month;
            
                $invoice->save();
    
                $sumTotal = 0;
                $sumSubtotal = 0;
                $sumIva = 0;
                    
                $inserts = array();
                for($j = 0; $j < $faker->numberBetween(5, 10); $j++) {
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
                  $is_exempt = false;
                  
                  $inserts[] = [
                    'invoice_id' => $invoice->id,
                    'company_id' => $company->id,
                    'item_number' => $item_number,
                    'code' => $code,
                    'name' => $name,
                    'product_type' => $product_type,
                    'measure_unit' => $measure_unit,
                    'item_count' => $item_count,
                    'unit_price' => $unit_price,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'discount_type' => '01',
                    'discount' => $discount_percentage,
                    'iva_type' => $iva_type,
                    'iva_percentage' => $iva_percentage,
                    'iva_amount' => $iva_amount,
                    'is_exempt' => false,
                    'is_identificacion_especifica' => false,
                    'year' => $invoice->year,
                    'month' => $invoice->month
                 ];
                  
                  $sumTotal = $sumTotal + $total;
                  $sumSubtotal = $sumSubtotal + $subtotal;
                  $sumIva = $sumIva + $iva_amount;
                }
                \App\InvoiceItem::insert($inserts);
                
                $invoice->total = $sumTotal;
                $invoice->subtotal = $sumSubtotal;
                $invoice->iva_amount = $sumIva;
              
                $invoice->generated_date = $generated_date;
                $invoice->due_date = Carbon::createFromFormat('d/m/Y g:i A', $fecha->format('d/m/Y g:i A') )->addDays(15);
                $invoice->save();
                
                $company->last_invoice_ref_number = $invoice->reference_number;
                $company->last_document = $invoice->document_number;
            
            });
        }
          
        $company->save();
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
