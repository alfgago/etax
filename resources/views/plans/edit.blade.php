@extends('layouts/app')

@section('title') 
Edit Subscription Plan
@endsection

@section('content') 

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>    
</div>
@endif


{!! Form::open(array('route' => ['plans.update', $plan->id],'method'=>'PATCH')) !!}
<div class="row">

    <div class="form-group col-md-12">
        <h3>PLAN DETAILS</h3>
    </div>

    <div class="form-group col-md-6">
        <strong>Plan Type *</strong>
        {!! Form::select('plan_type', get_plan_type(),$plan->plan_type, array('class' => 'form-control')) !!}
        @if ($errors->has('plan_type'))
        <span class="help-block">
            <strong>{{ $errors->first('plan_type') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-6">
        <strong>Plan Name *</strong>
        {!! Form::text('plan_name', $plan->plan_name, array('placeholder' => 'Plan Name','class' => 'form-control')) !!}
        @if ($errors->has('plan_name'))
        <span class="help-block">
            <strong>{{ $errors->first('plan_name') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-12">
        <h3>DOCUMENTATION</h3>
    </div>

    <div class="form-group col-md-6">
        <strong>Maximum of invoices *</strong>
        {!! Form::text('no_of_invoices', $plan->no_of_invoices, array('placeholder' => 'Maximum of invoices','class' => 'form-control')) !!}
        @if ($errors->has('no_of_invoices'))
        <span class="help-block">
            <strong>{{ $errors->first('no_of_invoices') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-6">
        <strong>Maximum of bills *</strong>
        {!! Form::text('no_of_bills', $plan->no_of_bills, array('placeholder' => 'Maximum of bills','class' => 'form-control')) !!}
        @if ($errors->has('no_of_bills'))
        <span class="help-block">
            <strong>{{ $errors->first('no_of_bills') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-12">
        <h3>USERS</h3>
    </div>

    <div class="form-group col-md-6">
        <strong>Users Administrator / Editor *</strong>
        {!! Form::text('no_of_admin_user', $plan->no_of_admin_user, array('placeholder' => 'Users Administrator / Editor','class' => 'form-control')) !!}
        @if ($errors->has('no_of_admin_user'))
        <span class="help-block">
            <strong>{{ $errors->first('no_of_admin_user') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-6">
        <strong>Users Read Only *</strong>
        {!! Form::text('no_of_normal_user', $plan->no_of_normal_user, array('placeholder' => 'Users Read Only','class' => 'form-control')) !!}
        @if ($errors->has('no_of_normal_user'))
        <span class="help-block">
            <strong>{{ $errors->first('no_of_normal_user') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-12">
        <h3>SUPPORT AND CUSTOMER SERVICE</h3>
    </div>

    <div class="form-group col-md-4">
        <strong>Chat Support *</strong>
        {!! Form::select('chat_support', default_dropdown_values(),$plan->chat_support, array('class' => 'form-control')) !!}
        @if ($errors->has('chat_support'))
        <span class="help-block">
            <strong>{{ $errors->first('chat_support') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-4">
        <strong>Calls per month *</strong>
        {!! Form::text('calls_per_month', $plan->calls_per_month, array('placeholder' => 'Calls per month','class' => 'form-control')) !!}
        @if ($errors->has('calls_per_month'))
        <span class="help-block">
            <strong>{{ $errors->first('calls_per_month') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-4">
        <strong>Additional calls rate(per call) *</strong>
        <div class="input-group">
            {!! Form::text('additional_call_rates', $plan->additional_call_rates, array('placeholder' => 'Calls per month','class' => 'form-control')) !!}            
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="icon-regular i-Dollar-Sign-2"></i>
                </button>
            </div>            
        </div>
        @if ($errors->has('additional_call_rates'))
        <span class="help-block">
            <strong>{{ $errors->first('additional_call_rates') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>Initial Setup (virtual or telephone session) *</strong>
        {!! Form::select('initial_setup_virtual', initial_setup(),$plan->initial_setup_virtual, array('class' => 'form-control')) !!}
        @if ($errors->has('initial_setup_virtual'))
        <span class="help-block">
            <strong>{{ $errors->first('initial_setup_virtual') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>Initial Setup (meeting face to face) *</strong>
        {!! Form::select('initial_setup_meeting', initial_setup(),$plan->initial_setup_meeting, array('class' => 'form-control')) !!}
        @if ($errors->has('initial_setup_meeting'))
        <span class="help-block">
            <strong>{{ $errors->first('initial_setup_meeting') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-12">
        <h3>CONFIGURATION</h3>
    </div>
    
    <div class="form-group col-md-4">
        <strong>Multicurrency (colons / dollars) *</strong>
        {!! Form::select('multicurrency', default_dropdown_values(),$plan->multicurrency, array('class' => 'form-control')) !!}
        @if ($errors->has('multicurrency'))
        <span class="help-block">
            <strong>{{ $errors->first('multicurrency') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>Advanced Electronic Invoicing Module *</strong>
        {!! Form::select('e_invoicing', default_dropdown_values(),$plan->e_invoicing, array('class' => 'form-control')) !!}
        @if ($errors->has('e_invoicing'))
        <span class="help-block">
            <strong>{{ $errors->first('e_invoicing') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>Pre-invoicing module *</strong>
        {!! Form::select('pre_invoicing', default_dropdown_values(),$plan->pre_invoicing, array('class' => 'form-control')) !!}
        @if ($errors->has('pre_invoicing'))
        <span class="help-block">
            <strong>{{ $errors->first('pre_invoicing') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>VAT draft declaration *</strong>
        {!! Form::select('vat_declaration', default_dropdown_values(),$plan->vat_declaration, array('class' => 'form-control')) !!}
        @if ($errors->has('vat_declaration'))
        <span class="help-block">
            <strong>{{ $errors->first('vat_declaration') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>Basic reports *</strong>
        {!! Form::select('basic_report', default_dropdown_values(),$plan->basic_report, array('class' => 'form-control')) !!}
        @if ($errors->has('basic_report'))
        <span class="help-block">
            <strong>{{ $errors->first('basic_report') }}</strong>
        </span>
        @endif
    </div>
    
    <div class="form-group col-md-4">
        <strong>Advanced reports (custom)  *</strong>
        {!! Form::select('custom_report', default_dropdown_values(),$plan->custom_report, array('class' => 'form-control')) !!}
        @if ($errors->has('custom_report'))
        <span class="help-block">
            <strong>{{ $errors->first('custom_report') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-12">
        <h3>PRICE</h3>
    </div>

    <div class="form-group col-md-4">
        <strong>Price per month *</strong>
        <div class="input-group">
            {!! Form::text('monthly_price', $plan->monthly_price, array('placeholder' => 'Price per month','class' => 'form-control')) !!}
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="icon-regular i-Dollar-Sign-2"></i>
                </button>
            </div>
        </div>
        @if ($errors->has('monthly_price'))
        <span class="help-block">
            <strong>{{ $errors->first('monthly_price') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-4">
        <strong>Price for 3 months *</strong>
        <div class="input-group">
            {!! Form::text('quaterly_price', $plan->quaterly_price, array('placeholder' => 'Price for 3 months','class' => 'form-control')) !!}
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="icon-regular i-Dollar-Sign-2"></i>
                </button>
            </div>
        </div>
        @if ($errors->has('quaterly_price'))
        <span class="help-block">
            <strong>{{ $errors->first('quaterly_price') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-4">
        <strong>Price for 6 months *</strong>
        <div class="input-group">
            {!! Form::text('half_yearly_price', $plan->half_yearly_price, array('placeholder' => 'Price for 6 months','class' => 'form-control')) !!}
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="icon-regular i-Dollar-Sign-2"></i>
                </button>
            </div>
        </div>
        @if ($errors->has('half_yearly_price'))
        <span class="help-block">
            <strong>{{ $errors->first('half_yearly_price') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group col-md-4">
        <strong>Price for 12 months *</strong>
        <div class="input-group">
            {!! Form::text('annual_price', $plan->annual_price, array('placeholder' => 'Price for 12 months','class' => 'form-control')) !!}
            <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                    <i class="icon-regular i-Dollar-Sign-2"></i>
                </button>
            </div>
        </div>
        @if ($errors->has('annual_price'))
        <span class="help-block">
            <strong>{{ $errors->first('annual_price') }}</strong>
        </span>
        @endif
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
{!! Form::close() !!}

@endsection