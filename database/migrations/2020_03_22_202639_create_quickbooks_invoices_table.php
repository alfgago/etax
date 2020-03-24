<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickbooksInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quickbooks_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            
            $table->integer('qb_id')->nullable();
            $table->string('qb_doc_number')->nullable();
            $table->timestamp('qb_date')->nullable();
            $table->double('qb_total')->nullable();
            $table->string('qb_client')->nullable();
            $table->string('qb_account')->nullable();
            $table->json('qb_data')->nullable();
            
            $table->string('generated_at')->default('quickbooks');
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_active')->default(false);
            
            $table->integer('month')->default(1);
            $table->integer('year')->default(2020);
            $table->index(['year', 'month']);
            
            $table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quickbooks_invoices');
    }
}
