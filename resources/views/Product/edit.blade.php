@extends('layouts/app')

@section('title') 
  Editar producto
@endsection

@section('content') 
<div class="row">
  <div class="col-md-12">
        
      <form method="POST" action="/productos/{{ $product->id }}">
        @method('patch')
        @csrf

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Información de producto
            </h3>
          </div>

          <div class="form-group col-md-6">
            <label for="code">Código</label>
            <input type="text" class="form-control" name="code" id="codigo" value="{{ $product->code }}" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" name="name" id="nombre" value="{{ $product->name }}" required>
          </div>
          
          <div class="form-group col-md-6">
            <label for="default_iva_type">Tipo de IVA</label>
            <select class="form-control select-search" name="default_iva_type" id="tipo_iva" >
              @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                <option value="{{ $tipo['code'] }}" attr-iva="{{ $tipo['percentage'] }}" porcentaje="{{ $tipo['percentage'] }}" class="{{ @$tipo['hidden'] ? 'hidden' : '' }}">{{ $tipo['name'] }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="product_category_id">Categoría de declaración</label>
            <select class="form-control" name="product_category_id" id="tipo_producto" >
              @foreach ( \App\ProductCategory::whereNotNull('invoice_iva_code')->get() as $tipo )
                <option value="{{ $tipo['id'] }}" codigo="{{ $tipo['invoice_iva_code'] }}" posibles="{{ $tipo['open_codes'] }}" >{{ $tipo['name'] }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="measure_unit">Unidad de medición</label>
            <select class="form-control" name="measure_unit" id="unidad_medicion" value="" required >
              @foreach ($units as $unit )
                <option value="{{ $unit['code'] }}" {{ $unit['code'] == $product->measure_unit ? 'selected' : '' }}  >{{ $unit['name'] }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="form-group col-md-6">
            <label for="unit_price">Precio unitario por defecto</label>
            <input type="text" class="form-control" name="unit_price" id="precio_unitario" value="{{ $product->unit_price }}" required>
          </div>
          
          <div class="form-group col-md-12">
            <label for="description">Descripción</label>
            <textarea class="form-control" name="description" id="descripcion" maxlength="160" max="160" style="resize:none;">{{ $product->description }}</textarea>
          </div>
          
        </div>

        <button id="btn-submit" type="submit" class="hidden">Guardar producto</button>
        
      </form> 
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar producto</button>
@endsection 
