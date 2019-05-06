@extends('layouts/app')

@section('title') 
Subscription Plan Details
@endsection

@section('content') 

<form>
    <div class="row">

        <div class="form-group col-md-12">
            <h3>PLAN DETAILS</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Plan Type:</strong>
            <label>{{ucwords($plan->plan_type)}}</label>        
        </div>

        <div class="form-group col-md-6">
            <strong>Plan Name:</strong>
            <label>{{$plan->plan_name}}</label>        
        </div>

        <div class="form-group col-md-12">
            <h3>DOCUMENTATION</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Maximum of invoices:</strong>
            <label>{{!is_null($plan->no_of_invoices) ? $plan->no_of_invoices:'Unlimited'}}</label>        
        </div>

        <div class="form-group col-md-6">
            <strong>Maximum of bills:</strong>
            <label>{{!is_null($plan->no_of_bills) ? $plan->no_of_bills:'Unlimited'}}</label>        
        </div>

        <div class="form-group col-md-12">
            <h3>LIMITATIONS</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>No. of Companies:</strong>
            <label>{{!is_null($plan->no_of_companies) ? $plan->no_of_companies:'Unlimited'}}</label>        
        </div>

        <div class="form-group col-md-6">
            <strong>No. of Invited admins:</strong>
            <label>{{!is_null($plan->no_of_admin_user) ? $plan->no_of_admin_user:'Unlimited'}}</label>    
        </div>

        <div class="form-group col-md-6">
            <strong>No. of Invited read-only users:</strong>
            <label>{{!is_null($plan->no_of_invited_user) ? $plan->no_of_invited_user:'Unlimited'}}</label>    
        </div>

        <div class="form-group col-md-12">
            <h3>SUPPORT AND CUSTOMER SERVICE</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Chat Support:</strong>
            <label>{{strtoupper($plan->chat_support)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Ticket SLA:</strong>
            <label>{{$plan->ticket_sla}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Calls per month:</strong>
            <label>{{!empty($plan->calls_per_month) ? $plan->calls_per_month:'N/A'}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Additional calls rate(per call):</strong>
            <label>{{!empty($plan->additional_call_rates) ? '$'.number_format( $plan->additional_call_rates):'N/A'}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Virtual setup help:</strong>
            <label>{{strtoupper($plan->initial_setup_virtual)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Face to face setup:</strong>
            <label>{{strtoupper($plan->initial_setup_meeting)}}</label>
        </div>

        <div class="form-group col-md-12">
            <h3>CONFIGURATION</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Multicurrency (colons / dollars):</strong>
            <label>{{strtoupper($plan->multicurrency)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Advanced Electronic Invoicing Module:</strong>
            <label>{{strtoupper($plan->e_invoicing)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Pre-invoicing module:</strong>
            <label>{{strtoupper($plan->pre_invoicing)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Tax declaration draft:</strong>
            <label>{{strtoupper($plan->vat_declaration)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Basic reports:</strong>
            <label>{{strtoupper($plan->basic_report)}}</label>
        </div>

        <div class="form-group col-md-6">
            <strong>Advanced reports:</strong>
            <label>{{strtoupper($plan->custom_report)}}</label>
        </div>

        <div class="form-group col-md-12">
            <h3>PRICE</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Price per month:</strong>
            <label>{{'$'.number_format( $plan->monthly_price)}}</label>        
        </div>

        <div class="form-group col-md-6">
            <strong>Price for 12 months:</strong>
            <label>{{'$'.number_format( $plan->annual_price)}}</label>        
        </div>
    </div>
</form>
@endsection