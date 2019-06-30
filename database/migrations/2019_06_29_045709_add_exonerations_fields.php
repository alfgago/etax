<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExonerationsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('exoneration_document_type')->nullable();
            $table->string('exoneration_document_number')->nullable();
            $table->string('exoneration_company_name')->nullable();
            $table->string('exoneration_porcent')->nullable();
            $table->double('exoneration_amount')->default(0);
            $table->double('impuestoNeto')->default(0);
            $table->double('exoneration_total_amount')->default(0);
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->string('exoneration_document_type')->nullable();
            $table->string('exoneration_document_number')->nullable();
            $table->string('exoneration_company_name')->nullable();
            $table->string('exoneration_porcent')->nullable();
            $table->double('exoneration_amount')->default(0);
            $table->double('impuestoNeto')->default(0);
            $table->double('exoneration_total_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('exoneration_document_type');
            $table->dropColumn('exoneration_document_number');
            $table->dropColumn('exoneration_company_name');
            $table->dropColumn('exoneration_porcent');
            $table->dropColumn('exoneration_amount');
            $table->dropColumn('impuestoNeto');
            $table->dropColumn('exoneration_total_amount');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('exoneration_document_type');
            $table->dropColumn('exoneration_document_number');
            $table->dropColumn('exoneration_company_name');
            $table->dropColumn('exoneration_porcent');
            $table->dropColumn('exoneration_amount');
            $table->dropColumn('impuestoNeto');
            $table->dropColumn('exoneration_total_amount');
        });
    }
}
