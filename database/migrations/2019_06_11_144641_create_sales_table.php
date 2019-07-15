<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('company_id')->default(0);
            $table->unsignedBigInteger('etax_product_id')->nullable();

            //1 = Activo, 2 = Activo con pago pendiente, 3 = Inactivo, 4 = En periodo de pruebas
            $table->enum('status', ['1', '2', '3', '4'])->default(1);

            //1 = Activo, 2 = Activo con pago pendiente, 3 = Inactivo, 4 = Cancelado
            $table->enum('recurrency', ['0', '1', '6', '12'])->default(0);

            $table->dateTime('trial_end_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('next_payment_date')->nullable();
            $table->dateTime('cancel_date')->nullable();
            $table->string('cancellation_token')->nullable();

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
        Schema::dropIfExists('sales');
    }
}
