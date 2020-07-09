<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoSocketDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('go_socket_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('document_id')->nullable(); 
            $table->string('type')->nullable(); 
            $table->unsignedBigInteger('invoice_id')->nullable(); 
            $table->unsignedBigInteger('bill_id')->nullable(); 
            $table->integer('attempts')->default(0);
            $table->text('gs_xml')->nullable(); 
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
            
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
        Schema::dropIfExists('go_socket_data');
    }
}
