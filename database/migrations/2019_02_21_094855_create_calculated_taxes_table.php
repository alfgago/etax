<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculatedTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculated_taxes', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            $table->string('month_code');
            $table->string('year');
            $table->boolean('is_cumulative')->default(false);
            $table->boolean('is_final_calculation')->default(false);
          
            $table->double('prorrata');
          
            $table->double('count_invoices');
            $table->double('count_invoice_items');
            $table->double('count_bills');
            $table->double('count_bill_items');
          
            $table->double('invoices_total');
            $table->double('bills_total');
          
            $table->double('invoices_subtotal');
            $table->double('bills_subtotal');
          
            $table->double('invoices_total_exempt');
            $table->double('deductable_iva');
            $table->double('non_deductable_iva');
            $table->double('total_invoice_iva');
            $table->double('total_bill_iva');
            $table->double('balance');
            $table->double('balance_real');
            $table->double('iva_deducible_anterior');
            $table->double('last_prorrata');
            $table->double('last_balance');
          
            $table->double('ratio1');
            $table->double('ratio2');
            $table->double('ratio3');
            $table->double('ratio4');
            $table->double('ratio_ex');
          
            //Debitos
            $table->double('b1')->default(0);
            $table->double('i1')->default(0);
            $table->double('b2')->default(0);
            $table->double('i2')->default(0);
            $table->double('b3')->default(0);
            $table->double('i3')->default(0);
            $table->double('b4')->default(0);
            $table->double('i4')->default(0);
            $table->double('b51')->default(0);
            $table->double('i51')->default(0);
            $table->double('b52')->default(0);
            $table->double('i52')->default(0);
            $table->double('b53')->default(0);
            $table->double('i53')->default(0);
            $table->double('b54')->default(0);
            $table->double('i54')->default(0);
            $table->double('b61')->default(0);
            $table->double('i61')->default(0);
            $table->double('b62')->default(0);
            $table->double('i62')->default(0);
            $table->double('b63')->default(0);
            $table->double('i63')->default(0);
            $table->double('b64')->default(0);
            $table->double('i64')->default(0);
            $table->double('b70')->default(0);
            $table->double('i70')->default(0);
            $table->double('b77')->default(0);
            $table->double('i77')->default(0);
            $table->double('b80')->default(0);
            $table->double('i80')->default(0);
            $table->double('b90')->default(0);
            $table->double('i90')->default(0);
            $table->double('b99')->default(0);
            $table->double('i99')->default(0);
          
            //Creditos
            $table->double('b101')->default(0);
            $table->double('i101')->default(0);
            $table->double('b102')->default(0);
            $table->double('i102')->default(0);
            $table->double('b103')->default(0);
            $table->double('i103')->default(0);
            $table->double('b104')->default(0);
            $table->double('i104')->default(0);
            $table->double('b121')->default(0);
            $table->double('i121')->default(0);
            $table->double('b122')->default(0);
            $table->double('i122')->default(0);
            $table->double('b123')->default(0);
            $table->double('i123')->default(0);
            $table->double('b124')->default(0);
            $table->double('i124')->default(0);
            $table->double('b130')->default(0);
            $table->double('i130')->default(0);
            $table->double('b141')->default(0);
            $table->double('i141')->default(0);
            $table->double('b142')->default(0);
            $table->double('i142')->default(0);
            $table->double('b143')->default(0);
            $table->double('i143')->default(0);
            $table->double('b144')->default(0);
            $table->double('i144')->default(0);
            $table->double('b150')->default(0);
            $table->double('i150')->default(0);
            $table->double('b160')->default(0);
            $table->double('i160')->default(0);
            $table->double('b199')->default(0);
            $table->double('i199')->default(0);
            $table->double('b200')->default(0);
            $table->double('i200')->default(0);
            $table->double('b201')->default(0);
            $table->double('i201')->default(0);
            $table->double('b240')->default(0);
            $table->double('i240')->default(0);
            $table->double('b250')->default(0);
            $table->double('i250')->default(0);
            $table->double('b260')->default(0);
            $table->double('i260')->default(0);
            $table->double('b299')->default(0);
            $table->double('i299')->default(0);
          
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
        Schema::dropIfExists('calculated_taxes');
    }
}
