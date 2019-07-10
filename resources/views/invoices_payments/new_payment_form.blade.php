<tr  id="row_{{@$i}}">
              <td><input class="amount"  type="number" name="amount[]" value=""></td>
              <td>
                <select name="payment_type[]">
                        <option value="0">Seleccione un metodo de pago</option>
                        <option value="01" >Efectivo</option>
                        <option value="02" >Tarjeta</option>
                        <option value="03" >Cheque</option>
                        <option value="04" >Tranferencia o Deposito Bancario</option>
                        <option value="05" >Recaudado por Terceros</option>
                        <option value="99" >Otros</option>

                  </select>
              </td>
              <td>
                  <select name="bank_account_id[]">
                        <option value="0">Seleccione una cuenta bancaria</option>
                      @foreach($cuentas as $cuenta)
                        <option value="{{@$cuenta->id}}" >{{@$cuenta->account}} {{@$cuenta->bank}} {{@$cuenta->currency}}
                        </option>
                      @endforeach
                  </select>
              </td>
              <td><input type="text" name="proof[]" value=""></td>
              <td><input type="date" name="payment_date[]" value=""></td>
              <td>
                  <select name="status[]">
                        <option value="1"  >Pendiente</option>
                        <option value="2"  >Consolidada</option>

                  </select>
              </td>
              <td><i class="fa fa-trash-o text-danger eliminar_row" row="{{@$i}}" aria-hidden="true"></i></td>
            </tr>
<script>
  funciones_tabla_payments_invoice();
</script>