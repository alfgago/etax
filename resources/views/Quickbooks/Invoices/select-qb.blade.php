<select class="form-control select-search" name="invoices[{{$facturaEtax->id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como factura nueva --</option>
    @foreach ( $facturasQb as $facturaQb )
      <option {{ $facturaQb->qb_id == $facturaEtax->qbid ? 'selected' : '' }} value="{{ $facturaQb->qb_id }}" >{{ $facturaQb->qb_doc_number }} / {{ $facturaQb->qb_client }}</option>
    @endforeach
 </select>