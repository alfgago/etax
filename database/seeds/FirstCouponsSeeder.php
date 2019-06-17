<?php

use Illuminate\Database\Seeder;

class FirstCouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Coupon::create([
            'code' => '$$$ETAX100DESCUENTO!',
            'promotion_name' => 'Equipo eTax',
            'discount_percentage' => 100,
            'months_duration' => 1200,
            'start_date' => \Carbon\Carbon::now(),
            'valid_until' => \Carbon\Carbon::now()->addYears(10),
            'use_once' => false,
            'used' => true
        ]);
        
        App\Coupon::create([
            'code' => '$$$ETAXTRANSFERENCIA!',
            'promotion_name' => 'Pago por transferencia',
            'discount_percentage' => 100,
            'months_duration' => 1,
            'start_date' => \Carbon\Carbon::now(),
            'valid_until' => \Carbon\Carbon::now()->addYears(10),
            'use_once' => false,
            'used' => true
        ]);
    }
}
