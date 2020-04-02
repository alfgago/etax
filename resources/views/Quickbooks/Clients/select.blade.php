<select class="form-control select-search" name="clients[{{$clienteQb->qb_id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como cliente nuevo --</option>
    @foreach ( $clientesEtax as $clienteEtax )
      <option {{ $clienteQb->client_id == $clienteEtax->id ? 'selected' : '' }} value="{{ $clienteEtax->id }}" >{{ $clienteEtax->toString() }}</option>
    @endforeach
 </select>