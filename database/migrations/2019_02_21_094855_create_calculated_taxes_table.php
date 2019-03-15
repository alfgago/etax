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
            $table->boolean('is_final')->default(false);
          
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
            $table->double('b001')->default(0);
            $table->double('i001')->default(0);
            $table->double('b002')->default(0);
            $table->double('i002')->default(0);
            $table->double('b003')->default(0);
            $table->double('i003')->default(0);
            $table->double('b004')->default(0);
            $table->double('i004')->default(0);
            $table->double('b051')->default(0);
            $table->double('i051')->default(0);
            $table->double('b052')->default(0);
            $table->double('i052')->default(0);
            $table->double('b053')->default(0);
            $table->double('i053')->default(0);
            $table->double('b054')->default(0);
            $table->double('i054')->default(0);
            $table->double('b061')->default(0);
            $table->double('i061')->default(0);
            $table->double('b062')->default(0);
            $table->double('i062')->default(0);
            $table->double('b063')->default(0);
            $table->double('i063')->default(0);
            $table->double('b064')->default(0);
            $table->double('i064')->default(0);
            $table->double('b070')->default(0);
            $table->double('i070')->default(0);
            $table->double('b077')->default(0);
            $table->double('i077')->default(0);
            $table->double('b080')->default(0);
            $table->double('i080')->default(0);
            $table->double('b090')->default(0);
            $table->double('i090')->default(0);
            $table->double('b099')->default(0);
            $table->double('i099')->default(0);
          
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
