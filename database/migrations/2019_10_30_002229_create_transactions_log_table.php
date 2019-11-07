<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_payment')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('id_paymethod')->nullable();
            $table->bigInteger('id_user')->nullable();
            $table->text('response')->nullable();
            $table->string('processor')->nullable();
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
        Schema::dropIfExists('transactions_log');
    }
}
