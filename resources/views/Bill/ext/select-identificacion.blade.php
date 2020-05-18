<div class="input-validate-iva">
 <select class="form-control porc_identificacion_plena hidden" name="items[{{ $item->id }}][porc_identificacion_plena]" placeholder="Seleccione identificacion especifica" required >
 	<option value="13" posibles="" {{$item->porc_identificacion_plena == 13 ? 'selected': ''}}>13%</option>
    <option value="5" posibles="" {{$item->porc_identificacion_plena == 0 || $item->porc_identificacion_plena == 5 ? 'selected': ''}}>0% con derecho a credito</option>
    <option value="99" posibles="" {{$item->porc_identificacion_plena == 99 ? 'selected': ''}}>0% no acreditable</option>
    <option value="1" posibles="" {{$item->porc_identificacion_plena == 1 ? 'selected': ''}}>1%</option>
    <option value="2" posibles="" {{$item->porc_identificacion_plena == 2 ? 'selected': ''}}>2%</option>
    <option value="4" posibles="" {{$item->porc_identificacion_plena == 4 ? 'selected': ''}}>4%</option>
  </select>
</div>