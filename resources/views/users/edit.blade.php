@extends('layouts/app')

@section('title') 
Edit User
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


{!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
<div class="row">

    <div class="form-group col-md-12">
        <h3>
            User Information
        </h3>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>First Name *</strong>
            {!! Form::text('first_name', $user->first_name, array('placeholder' => 'First Name','class' => 'form-control')) !!}
        </div>        
    </div>
    
    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>Last Name</strong>
            {!! Form::text('last_name', $user->last_name, array('placeholder' => 'Last Name','class' => 'form-control')) !!}
        </div>        
    </div>
    
    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>Email *</strong>
            {!! Form::text('email', $user->email, array('placeholder' => 'Email','class' => 'form-control')) !!}
        </div>        
    </div>
    
    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>Password</strong>
            {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
        </div>        
    </div>
    
    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>Confirm Password</strong>
            {!! Form::password('confirm_password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
        </div>        
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>Role *</strong>
            {!! Form::select('roles', array_merge(['' => 'Select Role'], $roles),$userRole, array('class' => 'form-control')) !!}
        </div>        
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
{!! Form::close() !!}

@endsection