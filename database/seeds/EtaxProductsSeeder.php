<?php

use Illuminate\Database\Seeder;

class EtaxProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 1,
            'isSubscription' => true,
            'name' => 'Profesional Básico',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 2,
            'isSubscription' => true,
            'name' => 'Profesional Intermedio',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 3,
            'isSubscription' => true,
            'name' => 'Profesional Pro',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 4,
            'isSubscription' => true,
            'name' => 'Empresarial Básico',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 5,
            'isSubscription' => true,
            'name' => 'Empresarial Intermedio',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 6,
            'isSubscription' => true,
            'name' => 'Empresarial Pro',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 7,
            'isSubscription' => true,
            'name' => 'Contador',
            'price' => 0,
        ]);
        
        App\EtaxProducts::create([
            'subscription_plan_id' => 8,
            'isSubscription' => true,
            'name' => 'Enterprise',
            'price' => 0,
        ]);
        
        //Productos comunes
        
        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Paquete de 5 facturas',
            'price' => 11.99,
        ]);

        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Paquete de 25 facturas',
            'price' => 15.99,
        ]);
        
        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Paquete de 50 facturas',
            'price' => 24.99,
        ]);    
        
        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Paquete de 250 facturas',
            'price' => 39.99,
        ]);

        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Paquete de 2000 facturas',
            'price' => 99.99,
        ]);
        
        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Paquete de 5000 facturas',
            'price' => 149.99,
        ]);  
        
        App\EtaxProducts::create([
            'isSubscription' => false,
            'name' => 'Prorrata 2018',
            'price' => 99.99,
        ]);   
        
    }
}
