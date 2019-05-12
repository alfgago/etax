@extends('layouts/app')

@section('title') 
Edit Subscription Plan
@endsection

@section('content') 

<form method="POST" action="{{route('plans.update',$plan->id)}}">
    @csrf
    @method('patch')
    <div class="row">

        <div class="form-group col-md-12">
            <h3>PLAN DETAILS</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Plan Type *</strong>
            <select class="form-control" name="plan_type">                
                @foreach(get_plan_type() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->plan_type) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('plan_type'))
            <span class="help-block">
                <strong>{{ $errors->first('plan_type') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-6">
            <strong>Plan Name *</strong>
            <input type="text" name="plan_name" class="form-control" placeholder="Plan Name" value="{{$plan->plan_name}}">

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
            <select class="form-control" name="no_of_invoices" <?php if (!empty($plan->no_of_invoices)) { ?>style="display:none;"<?php } ?> onchange="toggleSelect(this)">
                <option value="">Select</option>
                <option value="limited">Limited</option>
                <option value="-1" <?php if (empty($plan->no_of_invoices)) { ?>selected<?php } ?>>Unlimited</option>               
            </select>

            <input value="{{$plan->no_of_invoices}}" placeholder="Maximum no. of invoices" name="no_of_invoices" <?php if (empty($plan->no_of_invoices)) { ?>style="display:none;" disabled="disabled"<?php } ?> class="form-control" onblur="toggleInput(this)">

            @if ($errors->has('no_of_invoices'))
            <span class="help-block">
                <strong>{{ $errors->first('no_of_invoices') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-6">
            <strong>Maximum of bills *</strong>
            <select class="form-control" name="no_of_bills" <?php if (!empty($plan->no_of_bills)) { ?>style="display:none;"<?php } ?> onchange="toggleSelect(this)">
                <option value="">Select</option>
                <option value="limited">Limited</option>
                <option value="-1" <?php if (empty($plan->no_of_bills)) { ?>selected<?php } ?>>Unlimited</option>               
            </select>

            <input value="{{$plan->no_of_bills}}" placeholder="Maximum no. of bills" name="no_of_bills" <?php if (empty($plan->no_of_bills)) { ?>style="display:none;" disabled="disabled"<?php } ?> class="form-control" onblur="toggleInput(this)">

            @if ($errors->has('no_of_bills'))
            <span class="help-block">
                <strong>{{ $errors->first('no_of_bills') }}</strong>
            </span>
            @endif
        </div>        

        <div class="form-group col-md-12">
            <h3>LIMITATIONS</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>No. of Companies *</strong>
            <select class="form-control" name="no_of_companies" <?php if (!empty($plan->no_of_companies)) { ?>style="display:none;"<?php } ?> onchange="toggleSelect(this)">
                <option value="">Select</option>
                <option value="limited">Limited</option>
                <option value="-1" <?php if (empty($plan->no_of_companies)) { ?>selected<?php } ?>>Unlimited</option>               
            </select>

            <input value="{{$plan->no_of_companies}}" placeholder="Maximum no. of companies" name="no_of_companies" <?php if (empty($plan->no_of_companies)) { ?>style="display:none;" disabled="disabled"<?php } ?> class="form-control" onblur="toggleInput(this)">

            @if ($errors->has('no_of_companies'))
            <span class="help-block">
                <strong>{{ $errors->first('no_of_companies') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-6">
            <strong>No. of Invited admins *</strong>
            <select class="form-control" name="no_of_admin_user" <?php if (!empty($plan->no_of_admin_user)) { ?>style="display:none;"<?php } ?> onchange="toggleSelect(this)">
                <option value="">Select</option>
                <option value="limited">Limited</option>
                <option value="-1" <?php if (empty($plan->no_of_admin_user)) { ?>selected<?php } ?>>Unlimited</option>               
            </select>

            <input value="{{$plan->no_of_admin_user}}" placeholder="Maximum no. of invited admins" name="no_of_admin_user" <?php if (empty($plan->no_of_admin_user)) { ?>style="display:none;" disabled="disabled"<?php } ?> class="form-control" onblur="toggleInput(this)">

            @if ($errors->has('no_of_admin_user'))
            <span class="help-block">
                <strong>{{ $errors->first('no_of_admin_user') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-6">
            <strong>No. of Invited read-only users *</strong>
            <select class="form-control" name="no_of_invited_user" <?php if (!empty($plan->no_of_invited_user)) { ?>style="display:none;"<?php } ?> onchange="toggleSelect(this)">
                <option value="">Select</option>
                <option value="limited">Limited</option>
                <option value="-1" <?php if (empty($plan->no_of_invited_user)) { ?>selected<?php } ?>>Unlimited</option>               
            </select>

            <input value="{{$plan->no_of_invited_user}}" placeholder="Maximum no. of invited ready-only users" name="no_of_invited_user" <?php if (empty($plan->no_of_invited_user)) { ?>style="display:none;" disabled="disabled"<?php } ?> class="form-control" onblur="toggleInput(this)">

            @if ($errors->has('no_of_invited_user'))
            <span class="help-block">
                <strong>{{ $errors->first('no_of_invited_user') }}</strong>
            </span>
            @endif
        </div>        

        <div class="form-group col-md-12">
            <h3>SUPPORT AND CUSTOMER SERVICE</h3>
        </div>

        <div class="form-group col-md-4">
            <strong>Chat Support *</strong>
            <select class="form-control" name="chat_support">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->chat_support) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('chat_support'))
            <span class="help-block">
                <strong>{{ $errors->first('chat_support') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Ticket SLA *</strong>
            <input type="text" name="ticket_sla" class="form-control" placeholder="Ticket SLA" value="{{$plan->ticket_sla}}">

            @if ($errors->has('ticket_sla'))
            <span class="help-block">
                <strong>{{ $errors->first('ticket_sla') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Calls per month *</strong>
            <input type="text" name="calls_per_month" class="form-control" placeholder="Calls per month" value="{{$plan->calls_per_month}}">

            @if ($errors->has('calls_per_month'))
            <span class="help-block">
                <strong>{{ $errors->first('calls_per_month') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Additional calls rate(per call) *</strong>
            <div class="input-group">
                <input type="text" name="additional_call_rates" class="form-control" placeholder="Additional calls rate(per call)" value="{{$plan->additional_call_rates}}">                                
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
            <strong>Virtual setup help *</strong>
            <select class="form-control" name="initial_setup_virtual">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->initial_setup_virtual) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('initial_setup_virtual'))
            <span class="help-block">
                <strong>{{ $errors->first('initial_setup_virtual') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Face to face setup *</strong>
            <select class="form-control" name="initial_setup_meeting">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->initial_setup_meeting) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

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
            <select class="form-control" name="multicurrency">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->multicurrency) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('multicurrency'))
            <span class="help-block">
                <strong>{{ $errors->first('multicurrency') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Advanced Electronic Invoicing Module *</strong>
            <select class="form-control" name="e_invoicing">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->e_invoicing) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('e_invoicing'))
            <span class="help-block">
                <strong>{{ $errors->first('e_invoicing') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Pre-invoicing module *</strong>
            <select class="form-control" name="pre_invoicing">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->pre_invoicing) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('pre_invoicing'))
            <span class="help-block">
                <strong>{{ $errors->first('pre_invoicing') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Tax declaration draft *</strong>
            <select class="form-control" name="vat_declaration">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->vat_declaration) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('vat_declaration'))
            <span class="help-block">
                <strong>{{ $errors->first('vat_declaration') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Basic reports *</strong>
            <select class="form-control" name="basic_report">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->basic_report) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

            @if ($errors->has('basic_report'))
            <span class="help-block">
                <strong>{{ $errors->first('basic_report') }}</strong>
            </span>
            @endif
        </div>

        <div class="form-group col-md-4">
            <strong>Advanced reports (custom)  *</strong>
            <select class="form-control" name="custom_report">                
                @foreach(default_dropdown_values() as $key => $val)
                <option value="{{$key}}" <?php echo ($key == $plan->custom_report) ? 'selected' : ''; ?>>{{$val}}</option>
                @endforeach
            </select>

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
                <input type="text" name="monthly_price" class="form-control" placeholder="Price per month" value="{{$plan->monthly_price}}">                                
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
            <strong>Price for 12 months *</strong>
            <div class="input-group">
                <input type="text" name="annual_price" class="form-control" placeholder="Price for 12 months" value="{{$plan->annual_price}}">                                
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
</form>

@endsection