@extends('layouts/app')

@section('title') 
Comprar plan
@endsection

@section('content') 

<form method="POST" action="/purchase">
    @csrf
    <div class="row">

        <div class="form-group col-md-12">
            <h3>Detalles de plan</h3>
        </div>

        <div class="form-group col-md-6">
            <strong>Usuario *</strong>
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