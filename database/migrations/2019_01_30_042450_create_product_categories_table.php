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
          
            $table->string('name');
            $table->double('invoice_iva_code');
            $table->double('bill_iva_code');
            $table->integer('order')->default(0);
          
            $table->timestamps();
        });
      
        $lista = [
          ['nombre'=>'Bienes generales', 'invoice_iva_code'=>'103', 'bill_iva_code'=>'3'],
          ['nombre'=>'Exportaciones/Importaciones', 'invoice_iva_code'=>'150', 'bill_iva_code'=>'53'],
          ['nombre'=>'Servicios profesionales', 'invoice_iva_code'=>'103', 'bill_iva_code'=>'3'],
          ['nombre'=>'Servicios médicos', 'invoice_iva_code'=>'104', 'bill_iva_code'=>'4'],
          ['nombre'=>'Canasta básica', 'invoice_iva_code'=>'101', 'bill_iva_code'=>'1'],
          ['nombre'=>'Medicamentos', 'invoice_iva_code'=>'102', 'bill_iva_code'=>'2'],
          ['nombre'=>'Turismo', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'77'],
          ['nombre'=>'Libros', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'70'],
          ['nombre'=>'Arrendamiento de más de 640mil colones', 'invoice_iva_code'=>'130', 'bill_iva_code'=>'3'],
          ['nombre'=>'Arrendamiento de menos de 640mil colones', 'invoice_iva_code'=>'201', 'bill_iva_code'=>'70'],
          ['nombre'=>'Autoconsumo de servicios', 'invoice_iva_code'=>'123', 'bill_iva_code'=>'3'],
          ['nombre'=>'Energía residencial', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'70'],
          ['nombre'=>'Agua residencial', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'70'],
          ['nombre'=>'Sillas de ruedas y similares', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'70'],
          ['nombre'=>'Cuota de colegio profesional', 'invoice_iva_code'=>'200', 'bill_iva_code'=>'77'],
          ['nombre'=>'Otros', 'invoice_iva_code'=>'103', 'bill_iva_code'=>'3'],
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
