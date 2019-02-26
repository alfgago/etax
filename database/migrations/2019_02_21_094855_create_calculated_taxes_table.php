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
            $table->datetime('start_date');
            $table->datetime('end_date');
          
            $table->double('prorrata');
            $table->double('ratio1');
            $table->double('ratio2');
            $table->double('ratio3');
            $table->double('ratio4');
            $table->double('count_invoices');
            $table->double('count_invoice_items');
            $table->double('count_bills');
            $table->double('count_bill_items');
            $table->double('invoices_subtotal');
            $table->double('bills_subtotal');
            $table->double('invoices_total_not_exempt');
            $table->double('deductable_iva');
            $table->double('non_deductable_iva');
            $table->double('total_invoice_iva');
            $table->double('total_bill_iva');
            $table->double('balance');
            $table->double('iva_deducible_anterior');
            $table->double('last_prorrata');
            $table->double('last_balance');
          
            $table->boolean('is_cumulative')->default(false);
            $table->boolean('is_final_calculation')->default(false);
          
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
