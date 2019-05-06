<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSubscriptionsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions_history', function (Blueprint $table) {
            $table->bigIncrements('id');
			
			$table->string('unique_no');
			$table->integer('plan_id');
			$table->integer('user_id');
			$table->date('start_date');
			$table->date('expiry_date');
			$table->enum('status', ['0', '1'])->default(1);
			$table->string('cancellation_token')->nullable();			
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
