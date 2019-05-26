<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCompanyPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_company_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('company_id')->default(0);
			$table->unsignedBigInteger('user_id')->default(0);
			$table->integer('permission_id')->default(0);    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_company_permissions');
    }
}
