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
            $table->string('id_number')->nullable(); 
            $table->string('name')->nullable(); 
            $table->string('last_name')->nullable(); 
            $table->string('last_name2')->nullable(); 
            $table->string('email')->nullable(); 
            $table->boolean('confirmed_email')->default(false);
            $table->string('invoice_email')->nullable(); 
            $table->boolean('confirmed_invoice_email')->default(false);
            $table->string('country')->nullable(); 
            $table->string('state')->nullable(); //Provincia
            $table->string('city')->nullable(); //Canton
            $table->string('district')->nullable(); //Distrito
            $table->string('neighborhood')->nullable(); //Barrio
            $table->string('zip')->nullable();
            $table->string('address')->nullable(); 
            $table->string('phone')->nullable(); 
            $table->string('logo_url')->nullable();
            $table->string('default_currency')->default('crc');
            $table->string('last_document')->default('1000000000000000001');
            $table->integer('last_invoice_ref_number')->default(0);
            $table->integer('last_bill_ref_number')->default(0);
            $table->integer('tipo_calculo_prorrata')->default(0);
            
            $table->integer('workspace_year')->default(2019);
            $table->string('workspace_month')->default('a');
          
            $table->timestamps();
        });
        
        $user = App\User::create([
            'user_name' => 'alfgago',
            'email' => 'alfgago@gmail.com',
            'first_name' => 'Alfredo',
            'last_name' => 'Gago',
            'last_name2' => 'Jiménez',
            'password' => Hash::make('123456'),
        ]);
      
        App\Company::create([
            'user_id' => $user->id,
            'type' => 'juridica',
            'id_number' => '3-102-702429',
            'name' => '5E Labs',
            'email' => 'alfredo@5e.cr',
            'invoice_email' => 'info@5e.cr',
            'country' => 'Costa Rica',
            'address' => 'Pinares, Curridabat',
            'phone' => '2271-3298',
            'workspace_year' => '2019',
            'workspace_month' => '4'
        ]);
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
