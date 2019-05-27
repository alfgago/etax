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
          
            $table->unsignedBigInteger('company_id')->default(0);
            $table->string('tipo_persona')->nullable();
            $table->string('id_number')->nullable();
            $table->string('code')->default('');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('last_name2')->nullable();
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('emisor_receptor')->default('1');
            $table->string('country')->default('CR');
            $table->string('state')->nullable(); //Provincia
            $table->string('city')->nullable(); //Canton
            $table->string('district')->nullable(); //Distrito
            $table->string('neighborhood')->nullable(); //Barrio
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_area')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('es_exento')->default(false);
            $table->string('billing_emails')->nullable();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
          
            $table->timestamps();
        });
        
        if ( !app()->environment('production') ) {
            $this->demoData();
        }
    }
    
    public function demoData() {
        
        $company = \App\Company::first();
        $faker = Faker\Factory::create();
        $faker->addProvider(new Faker\Provider\es_ES\Person($faker));
      
        for($i = 0; $i < 50; $i++) {
            $client = new \App\Client();
            $client->company_id = $company->id;
            
            //Datos generales y para Hacienda
            $client->tipo_persona = "1";
            $client->id_number = $faker->numerify('###########');
            $client->code = $faker->lexify('???');
            $client->first_name = $faker->firstName;
            $client->last_name = $faker->lastName;
            $client->last_name2 = $faker->lastName;
            $client->email = $faker->freeEmail;
            $client->emisor_receptor = "ambos";
            $client->country = "CR";
            $client->state = "1";
            $client->city = "1";
            $client->district = "1";
            $client->neighborhood = "";
            $client->zip = "";
            $client->address = "";
            $client->phone = "";
            $client->es_exento = false;
            $client->billing_emails = $client->email;
            $client->fullname = $client->toString();
            
            $client->save();
        }
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
