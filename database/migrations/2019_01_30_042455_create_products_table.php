<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
          
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('measure_unit')->nullable();
            $table->double('unit_price')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_catalogue')->default(false);
            $table->string('default_iva_type')->nullable();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
          
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
        Schema::dropIfExists('products');
    }
}
