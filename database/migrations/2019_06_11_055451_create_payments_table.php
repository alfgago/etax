<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('sale_id');
			$table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('payment_method_id');

            $table->dateTime('payment_date')->nullable();
            $table->integer('payment_status')->default(1); //1: Pendiente, 2: Procesado, 0: Cancelado
            $table->string('nameCard')->nullable();
            $table->double('amount')->default(0);
            $table->string('proof')->nullable();

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
        Schema::dropIfExists('payments');
    }
}
