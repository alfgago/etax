<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            $table->enum('tipo_persona', ['fisica', 'juridica', 'dimex', 'extranjero', 'nite', 'otro']);
            $table->string('id_number');
            $table->string('code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_name2')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_emisor')->default(false);
            $table->boolean('is_receptor')->default(false);
            $table->string('country');
            $table->string('state')->nullable(); //Provincia
            $table->string('city')->nullable(); //Canton
            $table->string('district')->nullable(); //Distrito
            $table->string('neighborhood')->nullable(); //Barrio
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('es_exento');
          
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
        Schema::dropIfExists('clients');
    }
}
