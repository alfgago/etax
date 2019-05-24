<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('plan_id');
			
			//1 = Activo, 2 = Activo con pago pendiente, 3 = Inactivo, 4 = Cancelado
			$table->enum('status', ['1', '2', '3', '4'])->default(1);
			
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
        Schema::dropIfExists('user_subscriptions_history');
    }
}
