<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignAddressInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('foreign_address')->nullable();
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->string('foreign_address')->nullable();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('foreign_address')->nullable();
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->string('foreign_address')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function($table) {
            $table->dropColumn('foreign_address');
        });

        Schema::table('bills', function($table) {
            $table->dropColumn('foreign_address');
        });

        Schema::table('clients', function($table) {
            $table->dropColumn('foreign_address');
        });

        Schema::table('providers', function($table) {
            $table->dropColumn('foreign_address');
        });
    }
}
