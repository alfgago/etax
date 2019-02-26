<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['fisica', 'juridica', 'dimex', 'extranjero', 'nite', 'otro']); 
            $table->string('id_number');
            $table->string('name');
            $table->string('last_name')->nullable(); 
            $table->string('last_name2')->nullable(); 
            $table->string('email');
            $table->boolean('confirmed_email')->default(false);
            $table->string('invoice_email');
            $table->boolean('confirmed_invoice_email')->default(false);
            $table->string('country');
            $table->string('state'); //Provincia
            $table->string('city'); //Canton
            $table->string('district'); //Distrito
            $table->string('neighborhood'); //Barrio
            $table->string('zip')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('logo_url')->nullable();
            $table->string('default_currency')->default('crc');
            $table->string('last_document')->default('1000000000000000001');
            $table->integer('last_invoice__ref_number')->default(0);
            $table->integer('last_bill__ref_number')->default(0);
          
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
        Schema::dropIfExists('companies');
    }
}
