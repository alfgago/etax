<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherInvoiceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_invoice_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            
            $table->unsignedBigInteger('invoice_item_id')->nullable();
            $table->foreign('invoice_item_id')->references('id')->on('invoice_items'); //Se usa unicamente si esta asociado a una linea.
            
            $table->text('code')->nullable(); 
            $table->text('text')->nullable(); 
            $table->boolean('print_pdf')->default(true); 
            $table->boolean('print_xml')->default(false); 
            
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
        Schema::dropIfExists('other_invoice_data');
    }
}
