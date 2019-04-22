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
            
            $table->double('cc_compras')->nullable();
            $table->double('cc_importaciones')->nullable();
            $table->double('cc_propiedades')->nullable();
            $table->double('cc_iva_compras')->nullable();
            $table->double('cc_iva_importaciones')->nullable();
            $table->double('cc_iva_propiedades')->nullable();
            $table->double('cc_compras_sin_derecho')->nullable();
            $table->double('cc_proveedores_credito')->nullable();
            $table->double('cc_proveedores_contado')->nullable();
            
            $table->double('cc_ventas_1')->nullable();
            $table->double('cc_ventas_2')->nullable();
            $table->double('cc_ventas_13')->nullable();
            $table->double('cc_ventas_4')->nullable();
            $table->double('cc_ventas_exp')->nullable();
            $table->double('cc_ventas_estado')->nullable();
            $table->double('cc_ventas_1_iva')->nullable();
            $table->double('cc_ventas_2_iva')->nullable();
            $table->double('cc_ventas_13_iva')->nullable();
            $table->double('cc_ventas_4_iva')->nullable();
            $table->double('cc_ventas_sin_derecho')->nullable();
            $table->double('cc_ventas_sum')->nullable();
            $table->double('cc_clientes_credito')->nullable();
            $table->double('cc_clientes_contado')->nullable();
            $table->double('cc_clientes_credito_exp')->nullable();
            $table->double('cc_clientes_contado_exp')->nullable();
            $table->double('cc_clientes_sum')->nullable();
            
            $table->double('cc_ppp_1')->nullable();
            $table->double('cc_ppp_2')->nullable();
            $table->double('cc_ppp_3')->nullable();
            $table->double('cc_ppp_4')->nullable();
            $table->double('cc_bs_1')->nullable();
            $table->double('cc_bs_2')->nullable();
            $table->double('cc_bs_3')->nullable();
            $table->double('cc_bs_4')->nullable();
            $table->double('cc_iva_emitido_1')->nullable();
            $table->double('cc_iva_emitido_2')->nullable();
            $table->double('cc_iva_emitido_3')->nullable();
            $table->double('cc_iva_emitido_4')->nullable();
            $table->double('cc_aj_ppp_1')->nullable();
            $table->double('cc_aj_ppp_2')->nullable();
            $table->double('cc_aj_ppp_3')->nullable();
            $table->double('cc_aj_ppp_4')->nullable();
            $table->double('cc_aj_bs_1')->nullable();
            $table->double('cc_aj_bs_2')->nullable();
            $table->double('cc_aj_bs_3')->nullable();
            $table->double('cc_aj_bs_4')->nullable();
            $table->double('cc_ajuste_ppp')->nullable();
            $table->double('cc_ajuste_bs')->nullable();
            $table->double('cc_gasto_no_acreditable')->nullable();
            $table->double('cc_por_pagar')->nullable();
            $table->double('cc_sum1')->nullable();
            $table->double('cc_sum2')->nullable();
            
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
        Schema::dropIfExists('books')->nullable();
    }
}
