<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLastNoteRef extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasColumn('companies', 'last_note_ref_number'))
        {
            Schema::table('companies', function (Blueprint $table)
            {
                $table->dropColumn('last_note_ref_number');
            });
        }

        if (Schema::hasColumn('companies', 'last_document_note'))
        {
            Schema::table('companies', function (Blueprint $table)
            {
                $table->dropColumn('last_document_note');
            });
        }

        Schema::table('companies', function (Blueprint $table)
        {
            $table->bigInteger('last_document_note')->nullable();
            $table->bigInteger('last_note_ref_number')->nullable();
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
            $table->dropColumn('last_note_ref_number');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropColumn('last_document_note');
        });
    }
}
