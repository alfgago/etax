<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHaciendaResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hacienda_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills');
            
            $table->text('clave')->nullable(); 
            $table->text('nombre_emisor')->nullable(); 
            $table->text('tipo_identificacion_emisor')->nullable(); 
            $table->text('numero_cedula_emisor')->nullable(); 
            $table->text('nombre_receptor')->nullable(); 
            $table->text('tipo_identificacion_receptor')->nullable(); 
            $table->text('numero_cedula_receptor')->nullable(); 
            $table->text('mensaje')->nullable(); 
            $table->text('detalle_mensaje')->nullable(); 
            $table->text('monto_total_impuesto')->nullable(); 
            $table->text('total_factura')->nullable(); 
            $table->text('s3url')->nullable(); 
            
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
        Schema::dropIfExists('response_haciendas');
    }
}
