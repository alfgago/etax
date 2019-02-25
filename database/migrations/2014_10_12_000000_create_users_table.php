<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
          
            $table->string('user_name');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_name2')->nullable();
            $table->string('id_number');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('country');
            $table->string('state'); //Provincia
            $table->string('city'); //Canton
            $table->string('district'); //Distrito
            $table->string('neighborhood'); //Barrio
            $table->string('zip')->nullable();
            $table->string('address');
            $table->string('phone');
            $table->string('image_url');
          
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
