@extends('layouts/app')

@section('title') 
Create Permission
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


{!! Form::open(array('route' => 'permissions.store','method'=>'POST')) !!}
<div class="row">

    <div class="form-group col-md-12">
        <h3>
            Permission Information
        </h3>
    </div>

    <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="form-group">
            <strong>Permission Name *</strong>
            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
        </div>        
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
{!! Form::close() !!}

@endsection

@section('bottom-js')
<script src="{{asset('assets/js/form.validation.script.js')}}"></script>
@endsection
