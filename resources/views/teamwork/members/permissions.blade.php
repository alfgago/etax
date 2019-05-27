@extends('layouts/app')

@section('title')
Editar permisos de equipo "{{$team->name}}"
@endsection

@section('breadcrumb-buttons')
<a class="btn btn-primary" href="/empresas/equipo">Atrás</a>
@endsection

@section('content')

<style>
    .checkmark:after, .checkmark:before {
        display: none !important;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <form method="POST" action="/permissions/{{$team->id}}">
            @csrf

            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>                
                    <tr>
                        <th colspan="2">Invited Users</th>                             
                        @foreach($permissions as $permission)
                        <th>{{$permission->permission}}</th>                    
                        @endforeach                               
                    </tr>
                </thead>
                <tbody>
                    @if ( $team->users->count() )           
                    @foreach($team->users AS $user)
                    @if(auth()->user()->id != $user->id)
                    @php $user_permissions = get_user_company_permissions($team->company_id,$user->id)@endphp
                    <tr>
                        <td>{{$user->first_name.' '.$user->last_name.' '.$user->last_name2}}</td>
                        <td>{{$user->email}}</td>                    
                        @foreach($permissions as $permission)
                        <td><label class="checkbox checkbox-primary">
                                <input type="checkbox" name="permissions[<?php echo $user->id ?>][]" value="{{$permission->id}}" <?php echo in_array($permission->id, $user_permissions) ? 'checked' : ''; ?>>                
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        @endforeach                                   
                    </tr>
                    @endif
                    @endforeach
                    @endif
                </tbody>
            </table>
            @if ($team->users->count() > 1)
            <button type="submit" class="btn btn-primary">Submit</button>
            @endif
        </form>
        <input type="hidden" id="is-company-edit" value="3">
    </div>
</div>
@endsection
