<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastAceptLastNoteCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('last_rec_ref_number')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('last_document_rec')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('last_rec_ref_number');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn('last_document_rec');
        });
    }
}
