<select class="form-control select-search" name="products[{{$productoQb->qb_id}}]" placeholder="" required>
    <option class="crear-nuevo" value='N'>-- Guardar como producto nuevo --</option>
    @foreach ( $productosEtax as $productoEtax )
      <option {{ $productoQb->product_id == $productoEtax->id ? 'selected' : '' }} value="{{ $productoEtax->id }}" >{{ @$productoEtax->name }}</option>
    @endforeach
 </select>