<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_categories');
        Schema::create('product_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->string('name')->nullable();
            $table->string('declaracion_name')->nullable();
            $table->string('group')->nullable();
            $table->string('invoice_iva_code')->nullable();
            $table->string('bill_iva_code')->nullable();
            $table->string('open_codes')->nullable();
            $table->string('grupo')->nullable();
          
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
        Schema::dropIfExists('product_categories');
    }
}
