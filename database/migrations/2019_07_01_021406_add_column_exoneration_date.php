<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnExonerationDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->timestamp('exoneration_date')->nullable();
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->timestamp('exoneration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('exoneration_date');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn('exoneration_date');
        });
    }
}
