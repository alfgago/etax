@extends('layouts/app')

@section('title') 
Permissions
@endsection

@section('breadcrumb-buttons')
@can('permission-create')
<a type="submit" class="btn btn-primary" href="/permissions/create">Create New Permission</a>
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
                    <th>No</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
                </tr>
            </thead>
            <tbody>
                @if ( $permissions->count() )
                @foreach ($permissions as $permission)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>                        
                        @can('permission-edit')
                        <a href="{{route('permissions.edit',$permission->id)}}" title="Edit" class="text-success mr-2"> 
                            <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                        </a>
                        @endcan
                        @can('permission-delete')
                        <form class="inline-form" method="POST" action="{{route('permissions.destroy',$permission->id)}}" style="display: inline-block;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="text-danger mr-2" title="Delete" style="display: inline-block; background: none; border: 0;">
                                <i class="nav-icon i-Close-Window font-weight-bold"></i>
                            </button>
                        </form>
                        @endcan
                    </td>                   
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        
        {{ $permissions->links() }}
    </div>  
</div>
@endsection