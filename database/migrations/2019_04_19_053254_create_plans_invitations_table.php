<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans_invitations', function (Blueprint $table) {
            $table->bigIncrements('id');
			
			$table->unsignedBigInteger('subscription_id'); 
            $table->unsignedBigInteger('company_id'); 
            $table->unsignedBigInteger('user_id');        
			
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
        Schema::dropIfExists('plans_invitations');
    }
}
