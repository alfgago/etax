<div class="input-validate-iva">
  <select class="form-control iva_type" name="items[{{ $item->id }}][iva_type]" placeholder="Seleccione un código eTax" required >
      <?php
        $preselectos = array();
        foreach($company->soportados as $soportado){
          $preselectos[] = $soportado->id;
        }
      ?>
      @if(@$company->soportados[0]->id)
        @foreach ( $cat as $tipo )
            <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select {{ (in_array($tipo['id'], $preselectos) == false) ? 'hidden' : '' }}" {{$item->iva_type == $tipo->code ? 'selected' : ''}} >{{ $tipo['name'] }}</option>
        @endforeach
        <option class="mostrarTodos" value="1">Mostrar Todos</option>
      @else
        @foreach ( $cat as $tipo )
        <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select"  {{$item->iva_type == $tipo->code ? 'selected' : ''}}>{{ $tipo['name'] }}</option>
        @endforeach
      @endif
    </select>
</div>