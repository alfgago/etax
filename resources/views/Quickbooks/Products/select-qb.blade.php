<select class="form-control select-search" name="products[{{$productoEtax->id}}][qbid]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como producto nuevo --</option>
    @foreach ( $productosQb as $productoQb )
      <option {{ $productoQb->qb_id == $productoEtax->qbid ? 'selected' : '' }} value="{{ $productoQb->qb_id }}" >{{ $productoQb->name }}</option>
    @endforeach
 </select>