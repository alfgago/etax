@extends('layouts/app')@extends('layouts/app')

@section('title')
company Management
@endsection

@section('breadcrumb-buttons')
    @can('user-create')
    <a type="submit" class="btn btn-primary" href="/empresas/create">Create New company</a>
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
                    <th>Id Number</th>
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
                    <td>{{ $user->name.' '.$user->last_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->id_number }} </td>
                    <td>
                        @can('user-edit')
                        <a href="{{route('Company.edit')}}" title="Edit" class="text-success mr-2">
                           <i class="nav-icon i-Pen-2 font-weight-bold"></i>
                        </a>
                        @endcan
                        @can('user-delete')
                        <form class="inline-form" method="POST" action="{{route('empresas.destroy',$user->id)}}" style="display: inline-block;">
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

        {{ $data->links() }}
    </div>
</div>
@endsection
