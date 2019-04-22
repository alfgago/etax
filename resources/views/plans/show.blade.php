@extends('layouts/app')

@section('title') 
Subscription Plan Details
@endsection

@section('content') 

{!! Form::open() !!}
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
        <label>{{$plan->no_of_invoices}}</label>        
    </div>

    <div class="form-group col-md-6">
        <strong>Maximum of bills:</strong>
        <label>{{$plan->no_of_bills}}</label>        
    </div>

    <div class="form-group col-md-12">
        <h3>USERS</h3>
    </div>

    <div class="form-group col-md-6">
        <strong>Users Administrator / Editor:</strong>
        <label>{{$plan->no_of_admin_user}}</label>        
    </div>

    <div class="form-group col-md-6">
        <strong>Users Read Only:</strong>
        <label>{{$plan->no_of_normal_user}}</label>    
    </div>

    <div class="form-group col-md-12">
        <h3>SUPPORT AND CUSTOMER SERVICE</h3>
    </div>

    <div class="form-group col-md-6">
        <strong>Calls per month:</strong>
        <label>{{$plan->calls_per_month}}</label>
    </div>

    <div class="form-group col-md-6">
        <strong>Additional calls rate(per call):</strong>
        <label>{{'$'.number_format( $plan->additional_call_rates)}}</label>
    </div>

    <div class="form-group col-md-12">
        <h3>PRICE</h3>
    </div>

    <div class="form-group col-md-4">
        <strong>Price per month:</strong>
        <label>{{'$'.number_format( $plan->monthly_price)}}</label>        
    </div>

    <div class="form-group col-md-4">
        <strong>Price for 3 months:</strong>
        <label>{{'$'.number_format( $plan->quaterly_price)}}</label>        
    </div>

    <div class="form-group col-md-4">
        <strong>Price for 6 months:</strong>
        <label>{{'$'.number_format( $plan->half_yearly_price)}}</label>        
    </div>

    <div class="form-group col-md-4">
        <strong>Price for 12 months:</strong>
        <label>{{'$'.number_format( $plan->annual_price)}}</label>        
    </div>
</div>
{!! Form::close() !!}
@endsection