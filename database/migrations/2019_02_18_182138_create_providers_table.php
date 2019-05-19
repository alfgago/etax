<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->unsignedBigInteger('company_id');
            $table->string('tipo_persona');
            $table->string('id_number');
            $table->string('code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_name2')->nullable();
            $table->string('fullname')->nullable();
            $table->string('email');
            $table->string('country');
            $table->string('state')->nullable(); //Provincia
            $table->string('city')->nullable(); //Canton
            $table->string('district')->nullable(); //Distrito
            $table->string('neighborhood')->nullable(); //Barrio
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_area')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('es_exento')->default(false);
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->timestamps();
        });
        
        $this->demoData();
    }
    
    public function demoData() {
        
        $company = \App\Company::first();
        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\es_ES\Person($faker));
      
        for($i = 0; $i < 50; $i++) {
            $provider = new \App\Provider();
            $provider->company_id = $company->id;
            
            //Datos generales y para Hacienda
            $provider->tipo_persona = "1";
            $provider->id_number = $faker->numerify('###########');
            $provider->code = $faker->lexify('???');
            $provider->first_name = $faker->firstName;
            $provider->last_name = $faker->lastName;
            $provider->last_name2 = $faker->lastName;
            $provider->email = $faker->freeEmail;
            $provider->country = "CR";
            $provider->state = "1";
            $provider->city = "1";
            $provider->district = "1";
            $provider->neighborhood = "";
            $provider->zip = "";
            $provider->address = "";
            $provider->phone = "";
            $provider->fullname = $provider->toString();
            
            $provider->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers');
    }
}
