@extends('layouts/app')

@section('title') 
Edit Role
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

<form method="POST" action="{{route('roles.update',$role->id)}}">
    @csrf
    @method('patch')

    <div class="row">

        <div class="form-group col-md-12">
            <h3>
                Role Information
            </h3>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name *</strong>
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{$role->name}}">                
            </div>        
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Permission *</strong>
                <br/>
                @foreach($permission as $value)
                <label>
                    <input type="checkbox" name="permission[]" class="name" value="{{$value->id}}" <?php echo (in_array($value->id, $rolePermissions)) ? 'checked':'';?>>                    
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