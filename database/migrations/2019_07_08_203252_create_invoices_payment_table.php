<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->biginteger('invoice_id')->default(0);
            $table->float('amount')->nullable();
            $table->datetime('payment_date')->nullable();
            $table->enum('payment_type', ['01', '02', '03', '04', '05', '99'])->default('01');
            $table->integer('status')->default(1);
            $table->text('proof')->nullable();
            $table->biginteger('bank_account_id')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('invoices_payment');
    }
}
