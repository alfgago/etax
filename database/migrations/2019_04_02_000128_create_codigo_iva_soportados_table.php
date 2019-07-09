<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodigoIvaSoportadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('codigo_iva_soportados', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('bill_code')->nullable();
            $table->double('percentage')->default(0);
            $table->boolean('hidden')->default(false);
            $table->boolean('is_bienes')->default(false);
            $table->boolean('is_servicio')->default(false);
            $table->boolean('is_capital')->default(false);
            $table->boolean('is_identificacion_plena')->default(false);
            $table->boolean('is_importacion')->default(false);
            $table->boolean('is_gravado')->default(true);
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
        Schema::dropIfExists('codigo_iva_soportados');
    }
}
