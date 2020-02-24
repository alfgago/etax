<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuickbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id')->unique();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            $table->boolean('is_active')->default(true); 
            $table->string('qb_type')->default('qbo');
            $table->text('authorization_code')->nullable(); 
            $table->text('realm_id')->nullable(); 
            $table->text('refresh_token')->nullable(); 
            $table->text('access_token')->nullable(); 
            
            $table->string('invoice_from')->default('etax'); 
            $table->boolean('use_etax_currency')->default(true); 
            $table->boolean('sync_providers')->default(true); 
            $table->boolean('sync_clients')->default(true); 
            $table->boolean('sync_products')->default(true);  
            
            $table->text('conditions_json')->nullable(); 
            $table->text('payment_methods_json')->nullable(); 
            $table->text('taxes_json')->nullable(); 
            $table->text('clients_json')->nullable(); 
            $table->text('providers_json')->nullable(); 
            $table->text('products_json')->nullable(); 

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
        //
    }
}
