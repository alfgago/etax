<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculatedTaxesTable2 extends Migration
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
            
            /*$table->double('b001')->default(0);
            $table->double('i001')->default(0);
            $table->double('b002')->default(0);
            $table->double('i002')->default(0);
            $table->double('b003')->default(0);
            $table->double('i003')->default(0);
            $table->double('b004')->default(0);
            $table->double('i004')->default(0);
            
            $table->double('b008')->default(0);
            $table->double('i008')->default(0);
            
            $table->double('b011')->default(0);
            $table->double('i011')->default(0);
            $table->double('b012')->default(0);
            $table->double('i012')->default(0);
            $table->double('b013')->default(0);
            $table->double('i013')->default(0);
            $table->double('b014')->default(0);
            $table->double('i014')->default(0);
            
            $table->double('b018')->default(0);
            $table->double('i018')->default(0);
            
            $table->double('b021')->default(0);
            $table->double('i021')->default(0);
            $table->double('b022')->default(0);
            $table->double('i022')->default(0);
            $table->double('b023')->default(0);
            $table->double('i023')->default(0);
            $table->double('b024')->default(0);
            $table->double('i024')->default(0);
            
            $table->double('b028')->default(0);
            $table->double('i028')->default(0);
            
            $table->double('b031')->default(0);
            $table->double('i031')->default(0);
            $table->double('b032')->default(0);
            $table->double('i032')->default(0);
            $table->double('b033')->default(0);
            $table->double('i033')->default(0);
            $table->double('b034')->default(0);
            $table->double('i034')->default(0);
            
            $table->double('b038')->default(0);
            $table->double('i038')->default(0);
            
            $table->double('b040')->default(0);
            $table->double('i040')->default(0);
            $table->double('b041')->default(0);
            $table->double('i041')->default(0);
            $table->double('b042')->default(0);
            $table->double('i042')->default(0);
            $table->double('b043')->default(0);
            $table->double('i043')->default(0);
            $table->double('b044')->default(0);
            $table->double('i044')->default(0);
            
            $table->double('b048')->default(0);
            $table->double('i048')->default(0);
            
            $table->double('b050')->default(0);
            $table->double('i050')->default(0);
            $table->double('b051')->default(0);
            $table->double('i051')->default(0);
            $table->double('b052')->default(0);
            $table->double('i052')->default(0);
            $table->double('b053')->default(0);
            $table->double('i053')->default(0);
            $table->double('b054')->default(0);
            $table->double('i054')->default(0);
            
            $table->double('b058')->default(0);
            $table->double('i058')->default(0);
            
            $table->double('b060')->default(0);
            $table->double('i060')->default(0);
            $table->double('b061')->default(0);
            $table->double('i061')->default(0);
            $table->double('b062')->default(0);
            $table->double('i062')->default(0);
            $table->double('b063')->default(0);
            $table->double('i063')->default(0);
            $table->double('b064')->default(0);
            $table->double('i064')->default(0);
            
            $table->double('b068')->default(0);
            $table->double('i068')->default(0);
            
            $table->double('b070')->default(0);
            $table->double('i070')->default(0);
            $table->double('b071')->default(0);
            $table->double('i071')->default(0);
            $table->double('b072')->default(0);
            $table->double('i072')->default(0);
            $table->double('b073')->default(0);
            $table->double('i073')->default(0);
            $table->double('b074')->default(0);
            $table->double('i074')->default(0);
            
            $table->double('b078')->default(0);
            $table->double('i078')->default(0);
            
            $table->double('b080')->default(0);
            $table->double('i080')->default(0);
            $table->double('b090')->default(0);
            $table->double('i090')->default(0);
            $table->double('b097')->default(0);
            $table->double('i097')->default(0);
            $table->double('b098')->default(0);
            $table->double('i098')->default(0);
            $table->double('b099')->default(0);
            $table->double('i099')->default(0);*/
          
            //Creditos
            
            /*$table->double('b101')->default(0);
            $table->double('i101')->default(0);
            $table->double('b102')->default(0);
            $table->double('i102')->default(0);
            $table->double('b103')->default(0);
            $table->double('i103')->default(0);
            $table->double('b104')->default(0);
            $table->double('i104')->default(0);
            
            $table->double('b114')->default(0);
            $table->double('i114')->default(0);
            $table->double('b118')->default(0);
            $table->double('i118')->default(0);
            
            $table->double('b121')->default(0);
            $table->double('i121')->default(0);
            $table->double('b122')->default(0);
            $table->double('i122')->default(0);
            $table->double('b123')->default(0);
            $table->double('i123')->default(0);
            $table->double('b124')->default(0);
            $table->double('i124')->default(0);
            
            $table->double('b130')->default(0);
            $table->double('i130')->default(0);
            $table->double('b140')->default(0);
            $table->double('i140')->default(0);
            
            $table->double('b141')->default(0);
            $table->double('i141')->default(0);
            $table->double('b142')->default(0);
            $table->double('i142')->default(0);
            $table->double('b143')->default(0);
            $table->double('i143')->default(0);
            $table->double('b144')->default(0);
            $table->double('i144')->default(0);
            
            $table->double('b150')->default(0);
            $table->double('i150')->default(0);
            
            $table->double('b160')->default(0);
            $table->double('i160')->default(0);
            
            $table->double('b200')->default(0);
            $table->double('i200')->default(0);
            
            $table->double('b201')->default(0);
            $table->double('i201')->default(0);
            
            $table->double('b240')->default(0);
            $table->double('i240')->default(0);
            
            $table->double('b245')->default(0);
            $table->double('i245')->default(0);
            
            $table->double('b250')->default(0);
            $table->double('i250')->default(0);
            
            $table->double('b260')->default(0);
            $table->double('i260')->default(0);*/
          
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
