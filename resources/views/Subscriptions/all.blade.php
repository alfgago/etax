@extends('layouts/app')

@section('title') 
  	Todos los usuarios
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Cedula</th>
              <th data-priority="3">Nombre</th>
              <th>Correo</th>
              <th>Fecha</th>
              <th>Empresas</th>
            </tr>
          </thead>
          <tbody>
            @if ( $users->count() )
              @foreach ( $users as $data )
                <tr>
                  <td>{{ $data->id_number }}</td>
                  <td>{{ $data->first_name }} {{ $data->last_name }} {{ $data->last_name2 }}</td>
                  <td>{{ $data->email }}</td>
                  <td>{{ $data->created_at }}</td>
                  <td>
                  	@foreach ( $data->companies as $c )
                  		{{ $c->name }} ( {{ $c->business_name }} )<br>
                  	@endforeach
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        {{ $users->links() }}
  </div>  
</div>

@endsection