<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptedBillFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
             $table->string('commercial_activity')->nullable();
            $table->integer('xml_schema')->default('42'); // 42 = 4.2, 43 = 4.3
        });
        
        Schema::table('bills', function (Blueprint $table) {
            $table->string('commercial_activity')->nullable();
            $table->integer('accept_status')->default(0); //Codigo del mensaje de respuesta. 0: No ha elegido, 1 aceptado, 2 aceptado parcialmente, 3 rechazado
            $table->timestamp('accept_date')->nullable();
            $table->string('accept_details')->nullable();
            $table->string('accept_iva_condition')->nullable();
            $table->double('accept_iva_total')->default(0);
            $table->double('accept_iva_acreditable')->default(0);
            $table->double('accept_iva_gasto')->default(0);
            $table->double('accept_total_factura')->default(0);
            $table->string('accept_id_number')->nullable();
            $table->string('accept_consecutive')->default(0);
            $table->integer('xml_schema')->default('42'); // 42 = 4.2, 43 = 4.3
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function($table) {
            $table->dropColumn('commercial_activity');
        });
        
        Schema::table('bills', function($table) {
            $table->dropColumn('commercial_activity');
            $table->dropColumn('accept_status');
            $table->dropColumn('accept_date');
            $table->dropColumn('accept_details');
            $table->dropColumn('accept_iva_condition');
            $table->dropColumn('accept_iva_total');
            $table->dropColumn('accept_iva_acreditable');
            $table->dropColumn('accept_iva_gasto');
            $table->dropColumn('accept_total_factura');
            $table->dropColumn('accept_id_number');
            $table->dropColumn('accept_consecutive');
        });
    }
}
