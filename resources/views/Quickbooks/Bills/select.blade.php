<select class="form-control select-search" name="bills[{{$facturaQb->qb_id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como factura nueva --</option>
    @foreach ( $facturasEtax as $facturaEtax )
      <option {{ $facturaQb->bill_id == $facturaEtax->id ? 'selected' : '' }} value="{{ $facturaEtax->id }}" >{{ $facturaEtax->document_number  }} ({{ "$facturaEtax->provider_first_name $facturaEtax->provider_last_name $facturaEtax->provider_last_name2" }})</option>
    @endforeach
 </select>