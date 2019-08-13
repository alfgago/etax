<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillsToAvailableInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('available_invoices', function (Blueprint $table) {
            $table->integer('bill_monthly_quota')->default(0);
            $table->integer('bill_month_sent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('available_invoices', function (Blueprint $table) {
            //
        });
    }
}
