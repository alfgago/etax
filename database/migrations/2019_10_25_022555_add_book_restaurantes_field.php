<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBookRestaurantesField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void7
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('cc_restaurantes')->nullable();
            $table->string('cc_iva_restaurantes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('cc_restaurantes');
            $table->dropColumn('cc_iva_restaurantes');
        });
    }
}
