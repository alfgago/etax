<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdTypeClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('client_id_type')->nullable();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('client_id_type')->nullable();
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
            $table->dropColumn('client_id_type');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('client_id_type');
        });
    }
}
