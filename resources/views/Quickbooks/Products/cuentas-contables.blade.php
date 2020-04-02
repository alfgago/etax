<select class="form-control select-search" name="products[{{$productoEtax->id}}][account]" placeholder="" required>
    <option class="crear-nuevo" value='0'>-- Sin asignar --</option>
    @foreach ( $cuentasQb as $cuenta )
      <option {{ $productoEtax->accId == $cuenta->Id ? 'selected' : '' }} value="{{ $cuenta->Id }}" >{{ $cuenta->Classification }}: {{ $cuenta->FullyQualifiedName }}</option>
    @endforeach
</select>