<select class="form-control select-search" name="invoices[{{$facturaQb->qb_id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como factura nueva --</option>
    @foreach ( $facturasEtax as $facturaEtax )
      <option {{ $facturaQb->invoice_id == $facturaEtax->id ? 'selected' : '' }} value="{{ $facturaEtax->id }}" >{{ $facturaEtax->document_number  }} ({{ "$facturaEtax->client_first_name $facturaEtax->client_last_name $facturaEtax->client_last_name2" }})</option>
    @endforeach
 </select>