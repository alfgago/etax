<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceReferenceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_reference_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            
            $table->string('docType')->nullable(); 
            $table->string('number')->nullable(); 
            $table->string('code')->nullable(); 
            $table->string('reason')->nullable(); 
            $table->timestamp('generated_date')->nullable();
            
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
        Schema::dropIfExists('invoice_reference_data');
    }
}
