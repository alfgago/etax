<div class="input-validate-iva">
  <select curr="{{ $item->product_type }}" class="form-control product_type" name="items[{{ $item->id }}][product_type]" placeholder="Seleccione una categoría de hacienda" required>
      @foreach($categoriaProductos as $cat)
          <option value="{{@$cat->id}}" codigo="{{ @$cat->invoice_iva_code }}" posibles="{{@$cat->open_codes}}" {{ $item->product_type == @$cat->id ? 'selected' : '' }}>{{@$cat->name}}</option>
      @endforeach
  </select>
</div>