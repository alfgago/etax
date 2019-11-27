<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalculatedFieldsToBillItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('bill_items', function (Blueprint $table) {
            $table->double('iva_acreditable')->default(0);
            $table->double('iva_gasto')->default(0);
            $table->double('iva_devuelto')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn('iva_acreditable');
            $table->dropColumn('iva_gasto');
            $table->dropColumn('iva_devuelto');
        });
    }
}
