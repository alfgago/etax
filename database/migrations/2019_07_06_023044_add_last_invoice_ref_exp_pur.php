<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastInvoiceRefExpPur extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('last_invoice_pur_ref_number')->default(0);
            $table->string('last_document_invoice_pur')->default('00100001080000000000');
            $table->string('last_invoice_exp_ref_number')->default(0);
            $table->string('last_document_invoice_exp')->default('00100001090000000000');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function($table) {
            $table->dropColumn('last_invoice_pur_ref_number');
            $table->dropColumn('last_document_invoice_pur');
            $table->dropColumn('last_invoice_exp_ref_number');
            $table->dropColumn('last_document_invoice_exp');
        });
    }
}
