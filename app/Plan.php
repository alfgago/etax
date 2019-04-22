<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model {

    use SoftDeletes;

    protected $table = 'subscription_plans';
    protected $fillable = [
        'plan_type',
        'plan_name',
        'plan_slug',
        'no_of_admin_user',
        'no_of_normal_user',
        'no_of_invoices',
        'no_of_bills',
        'chat_support',
        'calls_per_month',
        'additional_call_rates',
        'initial_setup_virtual',
        'initial_setup_meeting',
        'multicurrency',
        'e_invoicing',
        'pre_invoicing',
        'vat_declaration',
        'basic_report',
        'custom_report',
        'monthly_price',
        'quaterly_price',
        'half_yearly_price',
        'annual_price',
    ];

}
