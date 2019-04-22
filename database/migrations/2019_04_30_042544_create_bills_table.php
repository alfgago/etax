<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Carbon\Carbon;

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
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers');
          
            //Tipo de documento de referencia. 01 Factura electrónica, 02 Nota de débito electrónica, 03 nota de crédito electrónica, 04 Tiquete electrónico, 05 Nota de despacho, 06 Contrato, 07 Procedimiento, 08 Comprobante emitido en contigencia, 99 Otros
            $table->string('document_type');
          
            $table->string('document_key')->nullable();
          
            $table->integer('reference_number')->nullable();
            $table->string('document_number')->nullable();
          
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
            $table->double('retention_percent')->default(0);
          
            $table->string('buy_order')->nullable();
            $table->string('send_emails')->nullable();
            $table->text('description')->nullable();
            $table->string('hacienda_status');
            $table->string('payment_status');
            $table->string('payment_receipt')->nullable();
            $table->timestamp('generated_date');
            $table->timestamp('due_date');
          
            $table->boolean('is_exempt')->default(false);
            $table->boolean('is_void')->default(false);
          
            //Estado de aceptación de la factura: 01 Pendiente, 02 Aceptada, 03 Rechazada, 04 Anulada, 05 Parcial
            $table->enum('status', ['01', '02', '03', '04', '05'])->default('01');
          
            $table->string('generation_method');
            $table->string('other_reference')->nullable();
            
            $table->unsignedBigInteger('other_document')->nullable();
          
            $table->timestamps();
        });
      
        Schema::create('bill_items', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('item_number');
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('product_type')->nullable();
            $table->integer('measure_unit')->nullable();
            $table->integer('item_count');
            $table->double('unit_price');
            $table->double('subtotal');
            $table->double('iva_amount');
            $table->double('total');
            $table->string('discount_type')->nullable();
            $table->double('discount')->default(0);
            $table->double('discount_reason')->nullable();
            $table->string('iva_type');
            $table->double('iva_percentage')->nullable();
            $table->boolean('is_exempt')->default(false);
            $table->integer('porc_identificacion_plena')->default(13);
            
            $table->integer('month');
            $table->integer('year');
            $table->index(['year', 'month']);
          
            $table->timestamps();
        });
      
        $this->demoData();
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
            
            $bill = new \App\Bill();
            $bill->company_id = $company->id;
            //Datos generales y para Hacienda
            $bill->document_type = "01";
            $bill->document_key = "";
            $bill->reference_number = $company->last_bill_ref_number + 1;
            
            $numero_doc = ((int)$company->last_document) + 1001;
            $bill->document_number = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);

            $bill->sale_condition = $faker->randomElement(array('01','02'));
            $bill->payment_type = $faker->randomElement(array('01','02','03','04','05','99'));
            $bill->credit_time = 0;
            $bill->buy_order = $faker->lexify('???');
            $bill->other_reference = '';
            $bill->hacienda_status = "01";
            $bill->payment_status = "01";
            $bill->payment_receipt = "VOUCHER-123451234512345";
            $bill->generation_method = "M";

            //Datos de cliente
            $bill->provider_id = $faker->numberBetween(1,50);

            //Datos de factura
            $bill->description = $faker->sentence;
            $bill->currency = 'CRC';
            $bill->currency_rate = '1';
            /*
            $bill->subtotal = $request->subtotal;
            $bill->total = $request->total;
            $bill->iva_amount = $request->iva_amount;
            */

            $bill->save();

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
              $iva_type = $productCategory->bill_iva_code;
              $iva_percentage = (double)\App\Variables::getTipoSoportadoIVAPorc( $iva_type );
              
              $measure_unit = '1';
              $item_count = $faker->numberBetween(1, 3);
              $unit_price = $faker->numberBetween(100, 15000);
              $subtotal = $item_count*$unit_price;
              
              $iva_amount = $subtotal * $iva_percentage / 100;
              $total = $subtotal + $iva_amount;
              $discount_percentage = '0';
              $discount_reason = '';
              $is_exempt = false;

              $inserts[] = [
                    'bill_id' => $bill->id,
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
                    'discount_reason' => $discount_reason,
                    'iva_type' => $iva_type,
                    'iva_percentage' => $iva_percentage,
                    'iva_amount' => $iva_amount,
                    'is_exempt' => false,
                    'year' => $generated_date->year,
                    'month' => $generated_date->month
                 ];
              
              $sumTotal = $sumTotal + $total;
              $sumSubtotal = $sumSubtotal + $subtotal;
              $sumIva = $sumIva + $iva_amount;
            }
            \App\BillItem::insert($inserts);
            
            $bill->total = $sumTotal;
            $bill->subtotal = $sumSubtotal;
            $bill->iva_amount = $sumIva;
          
            $bill->generated_date = $generated_date;
            $bill->due_date = Carbon::createFromFormat('d/m/Y g:i A', $fecha->format('d/m/Y g:i A') )->addDays(15);
            $bill->save();
          
            $company->last_bill_ref_number = $bill->reference_number;
            $company->save();
          
          });  
          
        }
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
