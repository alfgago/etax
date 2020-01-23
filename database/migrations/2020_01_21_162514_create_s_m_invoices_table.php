<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSMInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_m_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->string('batch')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_key')->nullable();
            
            $table->string('num_factura')->nullable();
            $table->string('num_objeto')->nullable();
            $table->string('fecha_emision')->nullable();
            $table->string('fecha_pago')->nullable();
            $table->string('condicion')->nullable();
            $table->string('medio_pago')->nullable();
            $table->string('moneda')->nullable();
            $table->string('tipo_id')->nullable();
            $table->string('doc_identificacion')->nullable();
            $table->string('nombre_tomador')->nullable();
            $table->string('telefono_habitacion')->nullable();
            $table->string('telefono_celular')->nullable();
            $table->string('correo')->nullable();
            $table->string('provincia')->nullable();
            $table->string('canton')->nullable();
            $table->string('distrito')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('des_direccion')->nullable();
            $table->string('cantidad')->nullable();
            $table->string('precio_unitario')->nullable();
            $table->string('impuesto')->nullable();
            $table->string('total')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('actividad_comercial')->nullable();
            $table->string('codigo_etax')->nullable();
            $table->string('categoria')->nullable();
            $table->string('refer_factura')->nullable();
            
            $table->integer('month')->default(1);
            $table->integer('year')->default(2020);
            $table->string('batch_repeated')->nullable();
            $table->index(['year', 'month', 'batch']);
            $table->softDeletes();
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
        Schema::dropIfExists('s_m_invoices');
    }
}
