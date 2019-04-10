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
          
            $table->string('month');
            $table->string('year');
            
            $table->double('cc_compras');
            $table->double('cc_importaciones');
            $table->double('cc_propiedades');
            $table->double('cc_iva_compras');
            $table->double('cc_iva_importaciones');
            $table->double('cc_iva_propiedades');
            $table->double('cc_compras_sin_derecho');
            $table->double('cc_proveedores_credito');
            $table->double('cc_proveedores_contado');
            
            $table->double('cc_ventas_1');
            $table->double('cc_ventas_2');
            $table->double('cc_ventas_13');
            $table->double('cc_ventas_4');
            $table->double('cc_ventas_exp');
            $table->double('cc_ventas_estado');
            $table->double('cc_ventas_1_iva');
            $table->double('cc_ventas_2_iva');
            $table->double('cc_ventas_13_iva');
            $table->double('cc_ventas_4_iva');
            $table->double('cc_ventas_sin_derecho');
            $table->double('cc_ventas_sum');
            $table->double('cc_clientes_credito');
            $table->double('cc_clientes_contado');
            $table->double('cc_clientes_credito_exp');
            $table->double('cc_clientes_contado_exp');
            $table->double('cc_clientes_sum');
            
            $table->double('cc_ppp_1');
            $table->double('cc_ppp_2');
            $table->double('cc_ppp_3');
            $table->double('cc_ppp_4');
            $table->double('cc_bs_1');
            $table->double('cc_bs_2');
            $table->double('cc_bs_3');
            $table->double('cc_bs_4');
            $table->double('cc_iva_emitido_1');
            $table->double('cc_iva_emitido_2');
            $table->double('cc_iva_emitido_3');
            $table->double('cc_iva_emitido_4');
            $table->double('cc_iva_emitido_1');
            $table->double('cc_aj_ppp_1');
            $table->double('cc_aj_ppp_2');
            $table->double('cc_aj_ppp_3');
            $table->double('cc_aj_ppp_4');
            $table->double('cc_aj_bs_1');
            $table->double('cc_aj_bs_2');
            $table->double('cc_aj_bs_3');
            $table->double('cc_aj_bs_4');
            $table->double('cc_ajuste_ppp');
            $table->double('cc_ajuste_bs');
            $table->double('cc_gasto_no_acreditable');
            $table->double('cc_por_pagar');
            $table->double('cc_sum1');
            $table->double('cc_sum2');
            
            
            $table->boolean('is_closed')->default(false);
            
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
