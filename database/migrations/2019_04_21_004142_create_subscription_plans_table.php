<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('plan_type');
            $table->string('plan_name');
            $table->string('plan_slug');
            $table->string('no_of_companies')->nullable();
            $table->string('no_of_admin_user')->nullable();
            $table->string('no_of_invited_user')->nullable();
            $table->string('no_of_invoices')->nullable();
            $table->string('no_of_bills')->nullable();
            $table->string('chat_support');
            $table->string('ticket_sla');
            $table->string('calls_per_month')->nullable();
            $table->string('additional_call_rates')->nullable();
            $table->string('initial_setup_virtual');
            $table->string('initial_setup_meeting');
            $table->string('multicurrency');
            $table->string('e_invoicing');
            $table->string('pre_invoicing');
            $table->string('vat_declaration');
            $table->string('basic_report');
            $table->string('custom_report');
            $table->string('monthly_price');
            $table->string('annual_price');
            $table->string('status');
                        
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
        Schema::dropSoftDeletes('subscription_plans');
    }
}
