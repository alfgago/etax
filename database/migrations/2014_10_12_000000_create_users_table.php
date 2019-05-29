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
          
            $table->string('id_number')->nullable()->unique();
            
            $table->string('user_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('last_name2')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable(); //Provincia
            $table->string('city')->nullable(); //Canton
            $table->string('district')->nullable(); //Distrito
            $table->string('neighborhood')->nullable(); //Barrio
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('image_url')->nullable();
            $table->string('security_question')->nullable();
            $table->string('security_answer')->nullable();
            
            $table->string('zendesk_id')->nullable();
          
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
