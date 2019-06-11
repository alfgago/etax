<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillProviderInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('provider_id_number')->nullable();
            $table->string('provider_first_name')->nullable();
            $table->string('provider_last_name')->nullable();
            $table->string('provider_last_name2')->nullable();
            $table->string('provider_email')->nullable();
            $table->string('provider_address')->nullable();
            $table->string('provider_country')->nullable();
            $table->string('provider_state')->nullable();
            $table->string('provider_city')->nullable();
            $table->string('provider_district')->nullable();
            $table->string('provider_phone')->nullable();
            $table->string('provider_zip')->nullable();
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
            $table->dropColumn('provider_id_number')->nullable();
            $table->dropColumn('provider_first_name');
            $table->dropColumn('provider_last_name');
            $table->dropColumn('provider_last_name2');
            $table->dropColumn('provider_email');
            $table->dropColumn('provider_address');
            $table->dropColumn('provider_country');
            $table->dropColumn('provider_state');
            $table->dropColumn('provider_city');
            $table->dropColumn('provider_district');
            $table->dropColumn('provider_zip');
            $table->dropColumn('provider_phone');
        });
    }
}
