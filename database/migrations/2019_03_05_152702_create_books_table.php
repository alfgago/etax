<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('calculated_tax_id');
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('calculated_tax_id')->references('id')->on('calculated_taxes')->onDelete('cascade');
            
            $table->double('cc_compras')->default(0);
            $table->double('cc_importaciones')->default(0);
            $table->double('cc_propiedades')->default(0);
            $table->double('cc_iva_compras')->default(0);
            $table->double('cc_iva_importaciones')->default(0);
            $table->double('cc_iva_propiedades')->default(0);
            $table->double('cc_compras_sin_derecho')->default(0);
            $table->double('cc_proveedores_credito')->default(0);
            $table->double('cc_proveedores_contado')->default(0);
            
            $table->double('cc_ventas_1')->default(0);
            $table->double('cc_ventas_2')->default(0);
            $table->double('cc_ventas_13')->default(0);
            $table->double('cc_ventas_4')->default(0);
            $table->double('cc_ventas_exp')->default(0);
            $table->double('cc_ventas_estado')->default(0);
            $table->double('cc_ventas_1_iva')->default(0);
            $table->double('cc_ventas_2_iva')->default(0);
            $table->double('cc_ventas_13_iva')->default(0);
            $table->double('cc_ventas_4_iva')->default(0);
            $table->double('cc_ventas_sin_derecho')->default(0);
            $table->double('cc_ventas_sum')->default(0);
            $table->double('cc_clientes_credito')->default(0);
            $table->double('cc_clientes_contado')->default(0);
            $table->double('cc_clientes_credito_exp')->default(0);
            $table->double('cc_clientes_contado_exp')->default(0);
            $table->double('cc_clientes_sum')->default(0);
            $table->double('cc_retenido')->default(0);
            
            $table->double('cc_ppp_1')->default(0);
            $table->double('cc_ppp_2')->default(0);
            $table->double('cc_ppp_3')->default(0);
            $table->double('cc_ppp_4')->default(0);
            $table->double('cc_bs_1')->default(0);
            $table->double('cc_bs_2')->default(0);
            $table->double('cc_bs_3')->default(0);
            $table->double('cc_bs_4')->default(0);
            $table->double('cc_iva_emitido_1')->default(0);
            $table->double('cc_iva_emitido_2')->default(0);
            $table->double('cc_iva_emitido_3')->default(0);
            $table->double('cc_iva_emitido_4')->default(0);
            $table->double('cc_aj_ppp_1')->default(0);
            $table->double('cc_aj_ppp_2')->default(0);
            $table->double('cc_aj_ppp_3')->default(0);
            $table->double('cc_aj_ppp_4')->default(0);
            $table->double('cc_aj_bs_1')->default(0);
            $table->double('cc_aj_bs_2')->default(0);
            $table->double('cc_aj_bs_3')->default(0);
            $table->double('cc_aj_bs_4')->default(0);
            $table->double('cc_ajuste_ppp')->default(0);
            $table->double('cc_ajuste_bs')->default(0);
            $table->double('cc_gasto_no_acreditable')->default(0);
            $table->double('cc_por_pagar')->default(0);
            $table->double('cc_sum1')->default(0);
            $table->double('cc_sum2')->default(0);
            
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
        Schema::dropIfExists('books');
    }
}
