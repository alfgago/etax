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
          
            $table->unsignedBigInteger('user_id')->default(0);;
            $table->unsignedBigInteger('subscription_id')->default(0);;
            
            $table->enum('type', ['F', 'J', 'D', 'E', 'N', 'O'])->nullable(); 
            $table->string('id_number')->unique()->nullable(); 
            $table->string('business_name')->nullable(); 
            $table->string('activities')->nullable(); 
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
            
            //Advanced config
            $table->string('default_currency')->default('crc');
            $table->string('default_invoice_notes')->nullable();
            $table->string('default_vat_code')->default(103);
            $table->string('last_document')->default('1000000000000000001');
            $table->integer('last_invoice_ref_number')->default(0);
            $table->integer('last_bill_ref_number')->default(0);
            $table->double('first_prorrata')->default(100);
            $table->integer('prorrata_type')->default(1); // 1 = Ingresar facturas, 2 = Ingresar totales, 3 = Manual 
            $table->integer('first_prorrata_type')->default(1); // 1 = Ingresar facturas, 2 = Ingresar totales, 3 = Manual 
            $table->boolean('use_invoicing')->default(true);
			$table->string('deactivation_token')->nullable();
			
			$table->boolean('wizard_finished')->default(false);

            $table->timestamps();
			$table->softDeletes();
        });
        
        $user = App\User::create([
            'user_name' => 'alfgago',
            'email' => 'alfgago@gmail.com',
            'first_name' => 'Alfredo',
            'last_name' => 'Gago',
            'last_name2' => 'Jiménez',
            'password' => Hash::make('123456'),
        ]);
      
        $company = App\Company::create([
            'user_id' => $user->id,
            'type' => 'J',
            'id_number' => '3102702429',
            'business_name' => '5E Sociedad de Responsabilidad Limitada',
            'name' => '5E Labs',
            'email' => 'alfredo@5e.cr',
            'invoice_email' => 'info@5e.cr',
            'country' => 'Costa Rica',
            'address' => 'Pinares, Curridabat',
            'phone' => '2271-3298'
        ]);
        
        $team = App\Team::create(
            [
                'name' => '(1) 3-102-702429',
                'owner_id' => $user->id,
                'company_id' => 1
            ]    
        );
		$team->company_id = 1;
        $team->save();
        
        $user->attachTeam($team);

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
