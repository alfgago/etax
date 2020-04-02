<select class="form-control select-search" name="providers[{{$proveedorEtax->id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como proveedor nuevo --</option>
    @foreach ( $proveedoresQb as $proveedorQb )
      <option {{ $proveedorQb->qb_id == $proveedorEtax->qbid ? 'selected' : '' }} value="{{ $proveedorQb->qb_id }}" >{{ $proveedorQb->full_name ." / ". $proveedorQb->email }}</option>
    @endforeach
 </select>