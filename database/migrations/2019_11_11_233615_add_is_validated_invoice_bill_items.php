<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsValidatedInvoiceBillItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('is_code_validated')->default(false);
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->boolean('is_code_validated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('is_code_validated');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn('is_code_validated');
        });
    }
}

