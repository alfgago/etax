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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('subscription_id')->nullable();
            
            $table->dateTime('payment_date')->nullable();
            $table->integer('payment_status')->default(1); //1: Pendiente, 2: Procesado, 0: Cancelado
            $table->string('next_payment_date')->nullable();
            $table->double('amount')->nullable();
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
