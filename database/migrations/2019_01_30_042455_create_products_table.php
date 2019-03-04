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
          
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('product_category_id')->nullable();
          
            $table->string('code');
            $table->string('name');
            $table->string('measure_unit');
            $table->double('unit_price');
            $table->string('description')->nullable();
            $table->boolean('is_catalogue')->default(false);
            $table->string('default_iva_type');
          
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
