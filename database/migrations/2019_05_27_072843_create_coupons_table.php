<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Carbon\Carbon;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscription_id')->nullable();
            
            $table->string('code')->nullable();
            $table->string('promotion_name')->nullable();
            $table->double('discount_percentage')->default(0);
            $table->timestamp('valid_until')->nullable();
            $table->integer('months_duration')->default(1);
            $table->timestamp('start_date')->nullable();
            $table->boolean('use_once')->default(false);
            $table->boolean('used')->default(false);
            
            $table->timestamps();
        });
        
        $faker = Faker\Factory::create();
        for($i = 0; $i < 500; $i++) {
            
            App\Coupon::create([
                'code' => $faker->lexify('??????'),
                'promotion_name' => 'Evento de lanzamiento',
                'discount_percentage' => '100',
                'months_duration' => '1',
                'use_once' => true,
                'valid_until' =>  Carbon::now()->addMonths(2)
            ]);
            
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
