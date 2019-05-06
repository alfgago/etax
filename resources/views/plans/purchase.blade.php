@extends('layouts/app')

@section('title') 
Purchase Subscription Plan
@endsection

@section('content') 

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{route('plans.purchase')}}">
    @csrf
    <div class="row">

        <div class="form-group col-md-12">
            <h3>PLAN DETAILS</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>User *</strong>
            <select class="form-control" name="user_id">                
                @if(auth()->user()->roles[0]->name == 'Super Admin')
                <option value="">Select</option>
                @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->first_name.' '.$user->last_name.' '.$user->last_name2}}</option>
                @endforeach
                @else
                <option value="{{auth()->user()->id}}">{{auth()->user()->first_name.' '.auth()->user()->last_name.' '.auth()->user()->last_name2}}</option>
                @endif
            </select>                        
        </div>

        <div class="form-group col-md-6">
            <strong>Plan Name *</strong>
            <select class="form-control" name="plan_id">
                <option value="">Select</option>
                @foreach($plans as $plan)
                <option value="{{$plan->id}}">{{ucfirst($plan->plan_type).'-'.$plan->plan_name}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

@endsection