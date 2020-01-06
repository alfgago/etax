<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperativeYearDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operative_year_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->integer('method')->default('3');  // 1 = Manual, 2 = Ingresar totales, 3 = Ingresar facturas periodo anterior
            
            $table->double('prorrata_operativa')->default(0.9999);
            $table->double('operative_ratio1')->default(0);
            $table->double('operative_ratio2')->default(0);
            $table->double('operative_ratio3')->default(1);
            $table->double('operative_ratio4')->default(0);
            $table->double('operative_ratio8')->default(0);
            $table->double('previous_balance')->default(0);
            $table->boolean('locked')->default(false);
            
            $table->integer('year')->default(2019); //El año anterior
            $table->index(['year']);
            
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
        Schema::dropIfExists('operative_year_data');
    }
}
