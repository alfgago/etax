@extends('layouts/app')

@section('title')
Team Members of company "{{$team->name}}"
@endsection

@section('breadcrumb-buttons')
<a class="btn btn-primary" href="{{route('teams.index')}}">Back</a>
@if(auth()->user()->isOwnerOfTeam($team))
<a class="btn btn-warning" href="{{route('teams.members.assign_permissions', $team)}}">Manage Users Permissions</a>
@endif
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">

        @if($message = Session::get('success'))
        <div class="alert alert-success">
            {{$message}}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
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
                            <button type="submit" class="text-danger mr-2" title="Delete" style="display: inline-block; background: none; border: 0;">
                                <i class="nav-icon i-Close-Window font-weight-bold"></i>
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

        @if(auth()->user()->roles[0]->name == 'Super Admin'||auth()->user()->roles[0]->name == 'Admin')
        <div class="col-md-12" style="padding: 2rem 10px;">
            <div class="car mb-4">
                <div class="car-body text-left">
                    <h3 class="card-title">Pending invitations</h3>
                    <div class="row">

                        <div class="col-sm-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>E-Mail</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                @foreach($team->invites AS $invite)
                                <tr>
                                    <td>{{$invite->email}}</td>
                                    <td>
                                        <a href="{{route('teams.members.resend_invite', $invite)}}" class="btn btn-sm btn-default">
                                            <i class="fa fa-envelope-o"></i> Resend invite
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

        <div class="col-md-12" style="padding: 10px;">

            <div class="car mb-4">
                <div class="car-body text-left">
                    <h3 class="card-title">Invite to team "{{$team->name}}"</h3>

                    <form class="form-horizontal" method="post" action="{{route('teams.members.invite', $team)}}">
                        {!! csrf_field() !!}

                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <strong>E-Mail Address *</strong>
                                    {!! Form::text('email', old('email'), array('placeholder' => 'E-Mail Address','class' => 'form-control','required')) !!}

                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-envelope-o"></i>Invite to Team</button>
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
@endsection
