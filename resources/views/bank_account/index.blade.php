@extends('layouts/app')

@section('title') 
  Cuentas bancarias
@endsection

@section('breadcrumb-buttons')        
      <a type="submit" class="btn btn-primary" href="/bank-account/crear">Ingresar cuenta nueva</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
      
      <table id="bank-account-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Banco</th>
              <th>Numero de cuenta</th>
              <th>Moneda</th>
              <th>Acciones</th>
            </tr>
             
          </thead>
          <tbody>
            @foreach($cuentas as $cuenta)

                <tr>
                    <td>{{@$cuenta->bank}}</td>
                    <td>{{@$cuenta->account}}</td>
                    <td>{{@$cuenta->currency}}</td>
                    <td class="text-center"><a href="/bank-account/editar/{{@$cuenta->id}}"><button class="btn btn-info">Editar</button></a></td>
                </tr>

            @endforeach
          </tbody>
      </table>
  </div>  
</div>

@endsection

@section('footer-scripts')
<script>
  
$(function() {
  $(function() {
  $('#bank-account-table').DataTable({
    language: {
      url: "/lang/datatables-es_ES.json",
    },
  });
});
  
});
  
</script>
@endsection