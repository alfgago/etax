@extends('layouts/app')

@section('title') 
Edit team {{$team->name}}
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

<form class="form-horizontal" method="post" action="{{route('teams.update', $team)}}">
    <input type="hidden" name="_method" value="PUT" />
    {!! csrf_field() !!}
    <div class="row">

        <div class="form-group col-md-12">
            <h3>
                Teams Information
            </h3>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Name *</strong>
                {!! Form::text('name', $team->name, array('placeholder' => 'Name','class' => 'form-control')) !!}
            </div>        
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

@endsection