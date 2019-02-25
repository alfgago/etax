<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineaFacturaRecibidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linea_factura_recibidas', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('factura_recibida_id');
            //$table->unsignedBigInteger('producto_id');
            $table->integer('numero_linea');
            $table->string('codigo');
            $table->string('nombre');
            $table->string('tipo_producto');
            $table->integer('unidad_medicion');
            $table->integer('cantidad');
            $table->double('precio_unitario');
            $table->double('subtotal');
            $table->double('total');
            $table->double('porc_descuento')->default(0);
            $table->double('razon_descuento')->nullable();
            $table->double('tipo_iva');
            $table->double('porc_iva');
            $table->boolean('esta_exonerado')->default(false);
          
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
        Schema::dropIfExists('linea_factura_recibidas');
    }
}
