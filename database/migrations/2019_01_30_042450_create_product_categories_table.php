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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->string('name')->nullable();
            $table->string('invoice_iva_code')->nullable();
            $table->string('bill_iva_code')->nullable();
            $table->integer('order')->default(0);
          
            $table->timestamps();
        });
      
        $lista = [
          ['nombre'=>'Bienes generales', 'invoice_iva_code'=>'103', 'bill_iva_code'=>'003'],
          ['nombre'=>'Servicios profesionales', 'invoice_iva_code'=>'103', 'bill_iva_code'=>'003'],
          ['nombre'=>'Exportaciones/Importaciones', 'invoice_iva_code'=>'150', 'bill_iva_code'=>'023'],
          ['nombre'=>'Servicios médicos', 'invoice_iva_code'=>'104', 'bill_iva_code'=>'004'],
          ['nombre'=>'Canasta básica', 'invoice_iva_code'=>'101', 'bill_iva_code'=>'001'],
          ['nombre'=>'Medicamentos', 'invoice_iva_code'=>'102', 'bill_iva_code'=>'002'],
          ['nombre'=>'Turismo', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'097'],
          ['nombre'=>'Libros', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'090'],
          ['nombre'=>'Arrendamientos con precios mayores a 1.5 salarios mínimos', 'invoice_iva_code'=>'130', 'bill_iva_code'=>'003'],
          ['nombre'=>'Arrendamientos con precios menores a 1.5 salarios mínimos', 'invoice_iva_code'=>'201', 'bill_iva_code'=>'090'],
          ['nombre'=>'Autoconsumo de servicios', 'invoice_iva_code'=>'123', 'bill_iva_code'=>'03'],
          ['nombre'=>'Energía residencial', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'090'],
          ['nombre'=>'Agua residencial', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'090'],
          ['nombre'=>'Sillas de ruedas y similares', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'090'],
          ['nombre'=>'Cuota de colegio profesional', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'097'],
          ['nombre'=>'Otros', 'invoice_iva_code'=>'103', 'bill_iva_code'=>'003'],
        ];
      
        for($i = 0; $i < count($lista); $i++) {
            App\ProductCategory::create([
                'name' => $lista[$i]['nombre'],
                'invoice_iva_code' => $lista[$i]['invoice_iva_code'],
                'bill_iva_code' => $lista[$i]['bill_iva_code'],
                'order' => $i
            ]);
        }
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
