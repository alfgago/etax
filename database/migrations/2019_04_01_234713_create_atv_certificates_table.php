<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtvCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atv_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id');
            
            $table->string('user')->nullable(); 
            $table->string('password')->nullable(); 
            $table->string('key_url')->nullable(); 
            $table->string('pin')->nullable(); 
            
            $table->string('generated_date')->nullable();
            $table->string('due_date')->nullable();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
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
        Schema::dropIfExists('atv_certificates');
    }
}
