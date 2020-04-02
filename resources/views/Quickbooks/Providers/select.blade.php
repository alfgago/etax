<select class="form-control select-search" name="providers[{{$proveedorQb->qb_id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como proveedor nuevo --</option>
    @foreach ( $proveedoresEtax as $proveedorEtax )
      <option {{ $proveedorQb->provider_id == $proveedorEtax->id ? 'selected' : '' }} value="{{ $proveedorEtax->id }}" >{{ $proveedorEtax->toString() }}</option>
    @endforeach
 </select>