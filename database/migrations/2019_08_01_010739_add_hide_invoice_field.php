<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHideInvoiceField extends Migration
{
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->boolean('hide_from_taxes')->default(false);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('hide_from_taxes')->default(false);
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
            $table->dropColumn('hide_from_taxes');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('hide_from_taxes');
        });
    }
}
