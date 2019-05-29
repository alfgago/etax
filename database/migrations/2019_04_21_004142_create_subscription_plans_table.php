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
            
            $table->string('plan_type')->nullable();
            $table->string('plan_tier')->nullable();
            $table->integer('num_companies')->default(0);
            $table->integer('num_users')->default(0);
            $table->integer('num_invoices')->default(0);
            $table->integer('num_bills')->default(0);
            $table->boolean('chat_support')->default(true);
            $table->integer('ticket_sla')->default(0);
            $table->integer('call_center')->default(true);
            $table->integer('call_center_vip')->default(false);
            $table->boolean('setup_help')->default(false);
            $table->boolean('multicurrency')->default(false);
            $table->boolean('e_invoicing')->default(true);
            $table->boolean('pre_invoicing')->default(true);
            $table->boolean('vat_declaration')->default(true);
            $table->boolean('basic_reports')->default(true);
            $table->boolean('intermediate_reports')->default(true);
            $table->boolean('advanced_reports')->default(true);
            $table->double('monthly_price')->default(0);
            $table->double('six_price')->default(0);
            $table->double('annual_price')->default(0);
                        
            $table->timestamps();
			$table->softDeletes();
        });
        
        $this->registerPlans();
        
    }
    
    public function registerPlans() {
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Profesional',
            'plan_tier' => 'Básico',
            'num_companies' => 1,
            'num_users' => 1,
            'num_invoices' => 5,
            'num_bills' => 40,
            'ticket_sla' => 24,
            'call_center' => true,
            'call_center_vip' => false,
            'setup_help' => false,
            'multicurrency' => false,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => false,
            'advanced_reports' => false,
            'monthly_price' => 11.99,
            'six_price' => 10.99,
            'annual_price' => 9.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Profesional',
            'plan_tier' => 'Intermedio',
            'num_companies' => 1,
            'num_users' => 1,
            'num_invoices' => 25,
            'num_bills' => 200,
            'ticket_sla' => 24,
            'call_center' => true,
            'call_center_vip' => false,
            'setup_help' => false,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => false,
            'advanced_reports' => false,
            'monthly_price' => 15.99,
            'six_price' => 13.99,
            'annual_price' => 12.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Profesional',
            'plan_tier' => 'Pro',
            'num_companies' => 1,
            'num_users' => 2,
            'num_invoices' => 50,
            'num_bills' => 400,
            'ticket_sla' => 12,
            'call_center' => true,
            'call_center_vip' => false,
            'setup_help' => false,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => false,
            'monthly_price' => 24.99,
            'six_price' => 21.99,
            'annual_price' => 19.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Empresarial',
            'plan_tier' => 'Básico',
            'num_companies' => 1,
            'num_users' => 2,
            'num_invoices' => 250,
            'num_bills' => 0,
            'ticket_sla' => 12,
            'call_center' => true,
            'call_center_vip' => false,
            'setup_help' => false,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => false,
            'monthly_price' => 39.99,
            'six_price' => 35.99,
            'annual_price' => 32.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Empresarial',
            'plan_tier' => 'Intermedio',
            'num_companies' => 1,
            'num_users' => 4,
            'num_invoices' => 2000,
            'num_bills' => 0,
            'ticket_sla' => 12,
            'call_center' => true,
            'call_center_vip' => true,
            'setup_help' => false,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => false,
            'monthly_price' => 99.99,
            'six_price' => 90.99,
            'annual_price' => 82.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Empresarial',
            'plan_tier' => 'Pro',
            'num_companies' => 1,
            'num_users' => 10,
            'num_invoices' => 5000,
            'num_bills' => 0,
            'ticket_sla' => 12,
            'call_center' => true,
            'call_center_vip' => true,
            'setup_help' => false,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => true,
            'monthly_price' => 149.99,
            'six_price' => 136.99,
            'annual_price' => 124.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Contador',
            'plan_tier' => 'Básico',
            'num_companies' => 10,
            'num_users' => 2,
            'num_invoices' => 0,
            'num_bills' => 0,
            'ticket_sla' => 6,
            'call_center' => true,
            'call_center_vip' => true,
            'setup_help' => true,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => true,
            'monthly_price' => 149.99,
            'six_price' => 136.99,
            'annual_price' => 124.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Contador',
            'plan_tier' => 'Intermedio',
            'num_companies' => 25,
            'num_users' => 5,
            'num_invoices' => 0,
            'num_bills' => 0,
            'ticket_sla' => 6,
            'call_center' => true,
            'call_center_vip' => true,
            'setup_help' => true,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => true,
            'monthly_price' => 299.99,
            'six_price' => 274.99,
            'annual_price' => 249.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Contador',
            'plan_tier' => 'Pro',
            'num_companies' => 50,
            'num_users' => 10,
            'num_invoices' => 0,
            'num_bills' => 0,
            'ticket_sla' => 1,
            'call_center' => true,
            'call_center_vip' => true,
            'setup_help' => true,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => true,
            'monthly_price' => 499.99,
            'six_price' => 457.99,
            'annual_price' => 415.99,
        ]);
        
        App\SubscriptionPlan::create([
            'plan_type' => 'Enterprise',
            'plan_tier' => 'Pro',
            'num_companies' => 0,
            'num_users' => 0,
            'num_invoices' => 0,
            'num_bills' => 0,
            'ticket_sla' => 1,
            'call_center' => true,
            'call_center_vip' => true,
            'setup_help' => true,
            'multicurrency' => true,
            'pre_invoicing' => true,
            'vat_declaration' => true,
            'basic_reports' => true,
            'intermediate_reports' => true,
            'advanced_reports' => true,
            'monthly_price' => 0,
            'six_price' => 0,
            'annual_price' => 0,
        ]);
        
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
