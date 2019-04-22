@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar certificado</button>
@endsection

@section('content')

<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
        <div class="col-3">
            <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/editar">Editar perfil de empresa</a>
                </li>
                <li>
                    <a class="nav-link " aria-selected="false" href="/empresas/configuracion">Configuración avanzada</a>
                </li>
                <li class="active">
                    <a class="nav-link active" aria-selected="true" href="/empresas/certificado">Certificado digital</a>
                </li>
                <li>
                    <a class="nav-link" aria-selected="false" href="/empresas/equipo">Equipo de trabajo</a>
                </li>
            </ul>
        </div>
        <div class="col-9">
          <div class="tab-content">       
						

                            <div class="col-md-12 col-sm-12">
                                <h3 class="card-title">Miembros de "{{$team->name}}"</h3>
                                @if(auth()->user()->isOwnerOfTeam($team))
                                
                                @endif
                                <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ( $team->users->count() )
                                        @foreach($team->users AS $user)
                                        <tr>
                                            <td>{{$user->first_name.' '.$user->last_name.' '.$user->last_name2}}</td>
                                            <td>{{$user->email}}</td>
                                            <td>
                                                @if(auth()->user()->isOwnerOfTeam($team))
                                                @if(auth()->user()->getKey() !== $user->getKey())
                                                <form style="display: inline-block;" action="{{route('teams.members.destroy', [$team, $user])}}" method="post">
                                                    {!! csrf_field() !!}
                                                    @method('delete')
                                                    
                                                        <button type="submit" class="text-danger mr-2" title="Eliminar" style="display: inline-block; background: none; border: 0;">
                                                            <a href="javascript:void(0)"><i class="nav-icon i-Close-Window font-weight-bold"></i></a>
                                                        </button>                                                    
                                                </form>
                                                @endif
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>

                                @if(auth()->user()->isOwnerOfTeam($team))
                                <div class="col-md-12" style="padding: 2rem 0px;">
                                    <div class="car mb-4">
                                        <div class="car-body text-left">
                                            <h3 class="card-title">Invitaciones pendientes</h3>
                                            <div class="row">

                                                <div class="col-sm-12">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Correo electrónico</th>
                                                                <th>Acción</th>
                                                            </tr>
                                                        </thead>
                                                        @foreach($team->invites AS $invite)
                                                        <tr>
                                                            <td>{{$invite->email}}</td>
                                                            <td>
                                                                <a href="{{route('teams.members.resend_invite', $invite)}}" class="btn btn-sm btn-default">
                                                                    <i class="fa fa-envelope-o"></i> Reenviar invitación
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12" style="padding: 0px;">

                                    <div class="car mb-4">
                                        <div class="car-body text-left">
                                            <h3 class="card-title">Invitar usuarios a "{{$team->name}}"</h3>

                                            <form class="form-horizontal" method="post" action="{{route('teams.members.invite', $team)}}">
                                                {!! csrf_field() !!}

                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6">
                                                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                                            <strong>Correo electrónico *</strong>
                                                            {!! Form::text('email', old('email'), array('placeholder' => 'Correo electrónico','class' => 'form-control','required')) !!}

                                                            @if ($errors->has('email'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('email') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-md-6 col-md-offset-4">
                                                            <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-envelope-o"></i>Enviar invitación</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

          </div>
        </div>
      </div>
    </div>
  </div>  
</div>       

@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar certificado</button>
@endsection 

@section('footer-scripts')

@endsection

