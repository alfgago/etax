<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculatedTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('calculated_taxes', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->integer('month')->default(0);
            $table->integer('year')->default(0);
            
            $table->boolean('is_rectification')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->boolean('is_final')->default(true);
            
            $table->double('count_invoices')->default(0);
            $table->double('count_bills')->default(0);
            
            $table->double('invoices_total')->default(0);
            $table->double('invoices_subtotal')->default(0);
            $table->double('total_invoice_iva')->default(0);
            $table->double('total_clientes_contado_exp')->default(0);
            $table->double('total_clientes_credito_exp')->default(0);
            $table->double('total_clientes_contado')->default(0);
            $table->double('total_clientes_credito')->default(0);
            $table->double('sum_repercutido1')->default(0);
            $table->double('sum_repercutido2')->default(0);
            $table->double('sum_repercutido3')->default(0);
            $table->double('sum_repercutido4')->default(0);
            $table->double('sum_repercutido_exento_con_credito')->default(0);
            $table->double('sum_repercutido_exento_sin_credito')->default(0);
            
            $table->double('bills_total')->default(0);
            $table->double('bills_subtotal')->default(0);
            $table->double('total_bill_iva')->default(0);
            $table->double('bases_identificacion_plena')->default(0);
            $table->double('bases_no_deducibles')->default(0);
            $table->double('iva_acreditable_identificacion_plena')->default(0);
            $table->double('iva_no_acreditable_identificacion_plena')->default(0);
            $table->double('total_proveedores_contado')->default(0);
            $table->double('total_proveedores_credito')->default(0);
            $table->double('iva_retenido')->default(0);
            
            $table->double('bills_subtotal1')->default(0);
            $table->double('bills_subtotal2')->default(0);
            $table->double('bills_subtotal3')->default(0);
            $table->double('bills_subtotal4')->default(0);
            
            $table->double('numerador_prorrata')->default(0);
            $table->double('denumerador_prorrata')->default(0);
            $table->double('prorrata')->default(0);
            $table->double('prorrata_operativa')->default(0);
            $table->double('subtotal_para_cfdp')->default(0);
            $table->double('cfdp')->default(0);
            $table->double('iva_deducible_estimado')->default(0);
            $table->double('balance_estimado')->default(0);
            $table->double('iva_deducible_operativo')->default(0);
            $table->double('balance_operativo')->default(0);
            $table->double('iva_no_deducible')->default(0);
            $table->double('iva_por_cobrar')->default(0);
            $table->double('iva_por_pagar')->default(0);
            $table->double('ratio1')->default(0);
            $table->double('ratio2')->default(0);
            $table->double('ratio3')->default(0);
            $table->double('ratio4')->default(0);
            $table->double('fake_ratio1')->default(0);
            $table->double('fake_ratio2')->default(0);
            $table->double('fake_ratio3')->default(0);
            $table->double('fake_ratio4')->default(0);
            $table->double('fake_ratio_exento_sin_credito')->default(0);
            $table->double('fake_ratio_exento_con_credito')->default(0);
            $table->double('bases_ventas_con_identificacion')->default(0);
            $table->double('ivas_ventas_con_identificacion')->default(0);
            
            $table->double('saldo_favor')->default(0);
            $table->double('saldo_favor_anterior')->default(0);

            //Debitos
            $table->longText('iva_data');
          
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calculated_taxes');
    }
}
