<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOtherChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills');
            
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->integer('item_number')->default(1);
            $table->string('document_type')->nullable();
            $table->string('provider_id_number')->nullable();
            $table->string('provider_name')->nullable();
            $table->text('description')->nullable();
            $table->double('percentage')->default(0);
            $table->double('amount')->default(0);
          
            $table->integer('month')->default(1);
            $table->integer('year')->default(2019);
            $table->index(['year', 'month']);
          
            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->double('total_otros_cargos')->default(0);
        });
        
        Schema::table('bills', function (Blueprint $table) {
            $table->double('total_otros_cargos')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('other_charges');
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('total_otros_cargos');
        });
        
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('total_otros_cargos');
        });
    }
}
