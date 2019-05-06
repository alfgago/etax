@extends('layouts/app')

@section('title') 
Create Role
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

<form method="POST" action="{{route('roles.store')}}">
@csrf

<div class="row">

    <div class="form-group col-md-12">
        <h3>
            Role Information
        </h3>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Role Name *</strong>
            <input type="text" name="name" class="form-control" placeholder="Name" value="{{old('name')}}">           
        </div>        
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Permission *</strong>
            <br/>
            @foreach($permission as $value)
            <label>
                <input type="checkbox" name="permission[]" class="name" value="{{$value->id}}">                               
                {{ $value->name }}</label>
            <br/>
            @endforeach
        </div>        
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
</form>

@endsection

@section('bottom-js')
<script src="{{asset('assets/js/form.validation.script.js')}}"></script>
@endsection