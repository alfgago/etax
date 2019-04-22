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
            $table->string('no_of_companies');
            $table->string('no_of_admin_user');
            $table->string('no_of_invited_user');
            $table->string('no_of_invoices');
            $table->string('no_of_bills');
            $table->string('chat_support');
            $table->string('ticket_sla');
            $table->string('calls_per_month');
            $table->string('additional_call_rates');
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
            
            $table->softDeletes();
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
        Schema::dropSoftDeletes('subscription_plans');
    }
}
