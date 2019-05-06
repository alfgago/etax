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
			
			$table->string('plan_no'); 
            $table->string('company_id'); 
            $table->string('user_id');
			$table->enum('is_admin', ['0', '1']);
			$table->enum('is_read_only', ['0', '1']);            
			
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
