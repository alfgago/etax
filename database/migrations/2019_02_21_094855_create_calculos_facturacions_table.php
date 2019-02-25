<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalculosFacturacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculos_facturacions', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('empresa_id');
            $table->datetime('fecha');
          
            $table->double('subtotal_recibido');
            $table->double('ratio1');
            $table->double('ratio2');
            $table->double('ratio3');
            $table->double('ratio4');
            $table->double('count_recibidas');
            $table->double('count_emitidas');
            $table->double('subtotal_emitido');
            $table->double('total_emitido_sin_exencion');
            $table->double('iva_deducible');
            $table->double('iva_no_deducible');
            $table->double('total_iva_repercutido');
            $table->double('total_iva_soportado');
            $table->double('liquidacion');
            $table->double('iva_deducible_anterior');
            $table->double('liquidacion_prorrata_anterior');
          
            $table->boolean('es_acumulado')->default(false);
          
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
        Schema::dropIfExists('calculos_facturacions');
    }
}
