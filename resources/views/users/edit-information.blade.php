@extends('layouts/app')

@section('title')
Edit Personal Information
@endsection

@section('breadcrumb-buttons')
<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar información</button>    
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">

        @if(session('success'))

        <div role="alert" class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{session('success')}}

        </div>
        @endif

        @if(session('error'))

        <div role="alert" class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{session('error')}}

        </div>
        @endif        

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/overview">Visión general</a>
                        </li>
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/general">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/planes">Mis Planes Suscritos</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       

                        <div class="tab-pane fade show active" role="tabpanel">
                            <h3 class="card-title">Información Personal</h3>

                            <form method="POST" action="{{ route('User.update_information', ['id' => Auth::user()->id]) }}">

                                @csrf
                                @method('patch') 

                                <div class="form-row">

                                    <div class="row">
                                        <div class="col-md-8 col-sm-8">

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputTestl4">First Name</label>
                                                    <?php $fname = isset(Auth::user()->first_name) ? Auth::user()->first_name : null; ?>
                                                    <input type="text" placeholder="First Name" name="first_name" class="form-control" value="{{$fname}}">

                                                    @if ($errors->has('first_name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-6 ">
                                                    <label for="inputText">First Surname</label>
                                                    <?php $lname = isset(Auth::user()->last_name) ? Auth::user()->last_name : null; ?>                                                    
                                                    <input type="text" placeholder="First Surname" name="last_name" class="form-control" value="{{$lname}}">
                                                    
                                                    @if ($errors->has('last_name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('last_name') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputTestl5">second Surname</label>
                                                    <?php $lname2 = isset(Auth::user()->last_name2) ? Auth::user()->last_name2 : null; ?>
                                                    <input type="text" placeholder="Second Surname" name="last_name2" class="form-control" value="{{$lname2}}">
                                                    
                                                    @if ($errors->has('last_name2'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('last_name2') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputText1">Cedula</label>
                                                    <?php $id_number = isset(Auth::user()->id_number) ? Auth::user()->id_number : null; ?>
                                                    <input type="text" placeholder="Identification Number" name="id_number" class="form-control" value="{{$id_number}}">
                                                    
                                                    @if ($errors->has('id_number'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('id_number') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputTestl5">Telefono</label>
                                                    <?php $mobile = isset(Auth::user()->phone) ? Auth::user()->phone : null; ?>
                                                    <input type="number" placeholder="Phone" name="phone" class="form-control" value="{{$mobile}}">
                                                    
                                                    @if ($errors->has('phone'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('phone') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputText1">Celular</label>
                                                    <?php $celular = isset(Auth::user()->celular) ? Auth::user()->celular : null; ?>
                                                    <input type="text" placeholder="Celular" name="celular" class="form-control" value="{{$celular}}">
                                                    
                                                    @if ($errors->has('celular'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('celular') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputTestl5">Email</label>
                                                    <?php $email = isset(Auth::user()->email) ? Auth::user()->email : null; ?>
                                                    <input type="text" placeholder="Email" name="email" class="form-control" value="{{$email}}">
                                                    
                                                    @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Direccion">Direccion</label>
                                                <?php $address = isset(Auth::user()->address) ? Auth::user()->address : null; ?>
                                                <textarea rows="5" cols="40" class="form-control" name="address" id="Direccion">{{$address}}</textarea>
                                                
                                                @if ($errors->has('address'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('address') }}</strong>
                                                </span>
                                                @endif
                                            </div>

                                        </div>

                                        <div class="col-md-4 col-sm-4">

                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label for="inputTestl4">Estado</label>
                                                    <?php $state = isset(Auth::user()->state) ? Auth::user()->state : null; ?>
                                                    <input type="text" placeholder="State" name="state" class="form-control" value="{{$state}}">
                                                    
                                                    @if ($errors->has('state'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('state') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-8 col-sm-8 d-flex flex-wrap">                                        
                                            <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>          
                                        </div>

                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

@section('breadcrumb-buttons')
<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración</button>
@endsection 

@section('footer-scripts')

@endsection