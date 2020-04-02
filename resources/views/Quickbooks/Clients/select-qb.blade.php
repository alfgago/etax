<select class="form-control select-search" name="clients[{{$clienteEtax->id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como cliente nuevo --</option>
    @foreach ( $clientesQb as $clienteQb )
      <option {{ $clienteQb->qb_id == $clienteEtax->qbid ? 'selected' : '' }} value="{{ $clienteQb->qb_id }}" >{{ $clienteQb->full_name ." / ". $clienteQb->email }}</option>
    @endforeach
 </select>