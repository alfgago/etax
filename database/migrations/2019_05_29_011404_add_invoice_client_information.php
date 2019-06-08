<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceClientInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('client_first_name')->nullable();
            $table->string('client_last_name')->nullable();
            $table->string('client_last_name2')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_address')->nullable();
            $table->string('client_country')->nullable();
            $table->string('client_state')->nullable();
            $table->string('client_city')->nullable();
            $table->string('client_district')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_zip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('client_first_name');
            $table->dropColumn('client_last_name');
            $table->dropColumn('client_last_name2');
            $table->dropColumn('client_email');
            $table->dropColumn('client_address');
            $table->dropColumn('client_country');
            $table->dropColumn('client_state');
            $table->dropColumn('client_city');
            $table->dropColumn('client_district');
            $table->dropColumn('client_zip');
            $table->dropColumn('client_phone');
        });
    }
}
