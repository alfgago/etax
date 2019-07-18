<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillsVerificationColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('bills', function (Blueprint $table) {
            $table->integer('product_category_verification')->default(0);
            $table->text('activity_company_verification')->nullable();
            $table->text('codigo_iva_verification')->nullable();
            $table->float('identificacion_plena_verification')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('product_category_verification');
            $table->dropColumn('activity_company_verification');
            $table->dropColumn('codigo_iva_verification');
            $table->dropColumn('identificacion_plena_verification');
        });
    }
}
