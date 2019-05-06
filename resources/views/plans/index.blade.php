@extends('layouts/app')

@section('title') 
Subscription Plans
@endsection

@section('breadcrumb-buttons')
@if(auth()->user()->roles[0]->name == 'Super Admin')
<a class="btn btn-primary" href="/plans/create">Crear plan de suscripción</a>
<a class="btn btn-warning" href="/show-plans">Agregar miembro</a>
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
                    <th>Plan Name</th>
                    <th>No. of Companies</th>
                    <th>No. of Invited admins</th>
                    <th>No. of Invited read-only users</th>
                    <th>Monthly Price</th>                    
                    <th>Annual Price</th>                              
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ( $data->count() )
                @foreach ( $data as $row )
                <tr>
                    <td>{{ ucfirst($row->plan_type).'-'.$row->plan_name }}</td>
                    <td>{{ !is_null($row->no_of_companies) ? $row->no_of_companies:'Unlimited' }}</td>
                    <td>{{ !is_null($row->no_of_admin_user) ? $row->no_of_admin_user:'Unlimited' }}</td>
                    <td>{{ !is_null($row->no_of_invited_user) ? $row->no_of_invited_user:'Unlimited' }}</td>
                    <td>{{ '$'.number_format( $row->monthly_price) }}</td>                    
                    <td>{{ '$'.number_format( $row->annual_price ) }}</td>                                  
                    <td>
                        <a href="/plans/{{ $row->id }}" title="View Plan" class="text-primary mr-2"> 
                            <i class="nav-icon i-Eye font-weight-bold"></i> 
                        </a>
                        <a href="/plans/{{ $row->id }}/edit" title="Edit Plan" class="text-success mr-2"> 
                            <i class="nav-icon i-Pen-2 font-weight-bold"></i> 
                        </a>
                        <form class="inline-form" method="POST" action="/plans/{{ $row->id }}" >
                            @csrf
                            @method('delete')
                            <button type="submit" class="text-danger mr-2" >
                                <i class="nav-icon i-Close-Window font-weight-bold"></i>
                            </button>
                        </form>
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