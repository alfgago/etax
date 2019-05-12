@extends('layouts/app')

@section('title') 
   Edición de permisos
@endsection

@section('content') 

<form method="POST" action="{{route('permissions.store')}}">
    @csrf

    <div class="row">

        <div class="form-group col-md-12">
            <h3>
                Edición de permisos
            </h3>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Nombre de permiso *</strong>
                <input type="text" name="name" class="form-control" placeholder="Name" value="{{old('name')}}">                
            </div>        
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12"                                                                                                >
            <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
    </div>
</form>

@endsection

@section('bottom-js')
<script src="{{asset('assets/js/form.validation.script.js')}}"></script>
@endsection
