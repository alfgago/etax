<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCorbanaSucursalField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasColumn('bills', 'sucursal')){
                $table->text('sucursal')->nullable();
            }
            $table->text('email_reception')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('sucursal');
            $table->dropColumn('email_reception');
        });
    }
}
