<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(EtaxProductsSeeder::class);
         $this->call(UnidadMedidaTableSeeder::class);
         $this->call(FirstCouponsSeeder::class);
         $this->call(CodigosSeeder::class);
         $this->call(ProductCategorySeed::class);
         $this->call(CreateClientPassport::class);
         $this->call(MenuSeeder::class);
    }
}
