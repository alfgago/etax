<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperativePercentagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operative_percentages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->string('method')->nullable()->default('1');  // 1 = Manual, 2 = Ingresar totales, 3 = Ingresar facturas 
            
            $table->double('prorrata_operativa')->default(0);
            $table->double('operative_ratio1')->default(0);
            $table->double('operative_ratio2')->default(0);
            $table->double('operative_ratio3')->default(0);
            $table->double('operative_ratio4')->default(0);
            $table->double('operative_ratio8')->default(0);
            
            $table->integer('year')->default(0);
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
        Schema::dropIfExists('operative_percentages');
    }
}
