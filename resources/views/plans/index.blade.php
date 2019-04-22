@extends('layouts/app')

@section('title') 
Subscription Plans
@endsection

@section('breadcrumb-buttons')        
<a type="submit" class="btn btn-primary" href="/plans/create">Create Subscription Plan</a>      
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

        <div style="margin: 1rem;"> -- Here are search filters --  </div>
        
        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">

            <thead>
                <tr>
                    <th>Plan Name</th>                    
                    <th>Monthly Price</th>
                    <th>Quaterly Price</th>
                    <th>Half Yearly Price</th>
                    <th>Annual Price</th>                              
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if ( $data->count() )
                @foreach ( $data as $row )
                <tr>
                    <td>{{ $row->plan_name }}</td>                    
                    <td>{{ number_format( $row->monthly_price) }}</td>
                    <td>{{ number_format( $row->quaterly_price) }}</td>
                    <td>{{ number_format( $row->half_yearly_price) }}</td>
                    <td>{{ number_format( $row->annual_price ) }}</td>                                  
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