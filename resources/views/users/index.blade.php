@extends('layouts/app')

@section('title') 
Users Management
@endsection

@section('breadcrumb-buttons')
@can('user-create')
<a type="submit" class="btn btn-primary" href="/users/create">Create New User</a>
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
                <tr>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Action</th>
                </tr>
                </tr>
                </tr>
            </thead>
            <tbody>
                @if ( $data->count() )
                @foreach ($data as $key => $user)
                <tr>
                    <td>{{ ++$i }}</td>                    
                    <td>{{ $user->first_name.' '.$user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                        <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                        @endif
                    </td>
                    <td>                        
                        @can('user-edit')                                                
                        <a href="{{route('users.edit',$user->id)}}" title="Edit" class="text-success mr-2"> 
                            <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                        </a>                        
                        @endcan
                        @can('user-delete')
                        @if($user->id != auth()->user()->id)
                        <form class="inline-form" method="POST" action="{{route('users.destroy',$user->id)}}" style="display: inline-block;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="text-danger mr-2" title="Delete" style="display: inline-block; background: none; border: 0;">
                                <i class="nav-icon i-Close-Window font-weight-bold"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </td>                   
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

        {{ $data->links() }}
    </div>  
</div>
@endsection