 @extends('layouts/app')

@section('title') 
  Crear cuenta bancaria
@endsection

@section('breadcrumb-buttons')        
      <a type="submit" class="btn btn-primary" href="/bank-account/">Cuentas bancarias</a>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
      <form method="POST" action="/bank-account/guardar">
        @csrf
        <div class="form-group">
          <label for="account">Numero de  cuenta</label>
          <input type="text" class="form-control" id="account" name="account" placeholder="585669822256" required>
        </div>
        <div class="form-group">
          <label for="bank">Banco</label>
          <input type="text" class="form-control" id="bank" name="bank" placeholder="Banco Nacional" required>
        </div>
        <div class="form-group">
          <label for="currency">Moneda</label>
          <select class="form-control" id="currency" name="currency" required>
            <option value="CRC">Colones</option>
            <option value="USD">Dolares Estaunidenses</option>
          </select>
        </div>

        <button id="btn-submit" type="submit" class="btn btn-success">Guardar</button>
      </form>
           
  </div>  
</div>

@endsection

@section('footer-scripts')
@endsection