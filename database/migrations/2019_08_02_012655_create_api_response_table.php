<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_id')->nullable();
            $table->string('bill_id')->nullable();
            $table->string('document_key')->nullable();
            $table->string('doc_type')->nullable();
            $table->longText('json_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_responses', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('invoice_id');
            $table->dropColumn('bill_id');
            $table->dropColumn('document_key');
            $table->dropColumn('doc_type');
            $table->dropColumn('json_response');
        });
    }
}
