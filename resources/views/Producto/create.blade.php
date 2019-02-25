@extends('layouts/app')

@section('title') 
  Crear producto
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-body">
        
      <form method="POST" action="/productos">

        @csrf

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Información de producto
            </h3>
          </div>

          <div class="form-group col-md-6">
            <label for="codigo">Código</label>
            <input type="text" class="form-control" name="codigo" id="codigo" value="" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="nombre" value="" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="unidad_medicion">Unidad de medición</label>
            <select class="form-control" name="unidad_medicion" id="unidad_medicion" value="" required>
              @foreach ( \App\Variables::unidadesMedicion() as $unidad )
                <option value="{{ $unidad['codigo'] }}" >{{ $unidad['nombre'] }}</option>
              @endforeach
            </select>
            
          </div>
          
          <div class="form-group col-md-6">
            <label for="precio_unitario">Precio unitario por defecto</label>
            <input type="text" class="form-control" name="precio_unitario" id="precio_unitario" value="" required>
          </div>
          
          <div class="form-group col-md-12">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" name="descripcion" id="descripcion" value="" ></textarea>
          </div>
          
          <div class="form-group col-md-6">
            <label for="tipo_producto">Tipo de producto</label>
            <select class="form-control" name="tipo_producto" id="tipo_producto" required>
              @foreach ( \App\Variables::tiposRepercutidos() as $tipo )
                <option value="{{ $tipo['nombre'] }}" attr-iva="{{ $tipo['codigo_iva'] }}" >{{ $tipo['nombre'] }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="tipo_iva_defecto">Tipo de IVA</label>
            <select class="form-control" name="tipo_iva_defecto" id="tipo_iva" required>
              @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
                <option value="{{ $tipo['codigo'] }}" attr-iva="{{ $tipo['porcentaje'] }}">{{ $tipo['nombre'] }}</option>
              @endforeach
            </select>
          </div>

        </div>

        <button type="submit" class="btn btn-primary">Crear factura</button>

        @if ($errors->any())
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        @endif
        
      </form>
      </div>  
    </div>  
  </div>  
</div>
@endsection

@section('footer-scripts')
<script src="/js/form-facturas.js"></script>
<script>
$('#tipo_iva').val(103);
</script>
@endsection