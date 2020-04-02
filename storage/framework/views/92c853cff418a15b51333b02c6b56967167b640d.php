<?php $__env->startSection('title'); ?> 
  Editar factura emitida
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/<?php echo e($invoice->id); ?>">
        <?php echo method_field('patch'); ?>
        <?php echo csrf_field(); ?>

          <input type="hidden" id="current-index" value="<?php echo e(count($invoice->items)); ?>">

          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Cliente
                      </h3>
                      <div onclick="abrirPopup('nuevo-cliente-popup');" class="btn btn-agregar btn-agregar-cliente">Nuevo cliente</div>
                    </div>  
                    
                    <div class="form-group col-md-12 with-button">
                      <label for="cliente">Seleccione el cliente</label>
                      <select class="form-control select-search" name="client_id" id="client_id" placeholder="" required>
                        <option value=''>-- Seleccione un cliente --</option>
                        <?php $__currentLoopData = currentCompanyModel()->clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option  <?php echo e($invoice->client_id == $cliente->id ? 'selected' : ''); ?> value="<?php echo e($cliente->id); ?>" ><?php echo e($cliente->toString()); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>
                  </div>
                </div>
                    
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Moneda
                      </h3>
                    </div>
      
                    <div class="form-group col-md-4">
                      <label for="currency">Divisa</label>
                      <select class="form-control" name="currency" id="moneda" required>
                        <option value="CRC" <?php echo e($invoice->currency == 'CRC' ? 'selected' : ''); ?>>CRC</option>
                        <option value="USD" <?php echo e($invoice->currency == 'USD' ? 'selected' : ''); ?>>USD</option>
                      </select>
                    </div>
  
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" name="currency_rate" id="tipo_cambio" value="<?php echo e($invoice->currency_rate); ?>" required>
                    </div>
                  </div>
                </div>
              </div>  

              <div class="form-row">    
                <div class="form-group col-md-12">
                  <h3>
                    Total de factura
                  </h3>
                </div>
      
                 <div class="form-group col-md-4">
                  <label for="subtotal">Subtotal </label>
                  <input type="text" class="form-control" name="subtotal" id="subtotal" placeholder="" readonly="true" required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="iva_amount">Monto IVA </label>
                  <input type="text" class="form-control" name="iva_amount" id="monto_iva" placeholder="" readonly="true" required>
                </div>

              <div class="form-group col-md-4 hidden" id="total_iva_devuelto-cont">
                  <label for="total">IVA Devuelto</label>
                  <input type="text" class="form-control total" name="total_iva_devuelto" id="total_iva_devuelto" placeholder="" readonly="true" required>
              </div>

              <div class="form-group col-md-4 hidden" id="total_iva_exonerado-cont">
                <label for="total">IVA Exonerado</label>
                <input type="text" class="form-control total" name="total_iva_exonerado" id="total_iva_exonerado" placeholder="" readonly="true" required>
              </div>

              <div class="form-group col-md-4 hidden" id="total_otros_cargos-cont">
                <label for="total">Otros cargos</label>
                <input type="text" class="form-control total" name="total_otros_cargos" id="total_otros_cargos" placeholder="" readonly="true" required>
              </div>
      
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="agregarNuevaLinea();" class="btn btn-dark btn-agregar">Agregar línea</div>
                  <div onclick="abrirPopup('otros-popup');" class="btn btn-dark btn-agregar btn-otroscargos">Agregar otros cargos</div>
                </div>
      
              </div>
                
            </div>
            
            <div class="col-md offset-md-1">  
              <div class="form-row">
                <div class="form-group col-md-12">
                  <h3>
                    Datos generales
                  </h3>
                </div>
                
                  <div class="form-group col-md-6">
                    <label for="document_number">Número de documento</label>
                    <input type="text" class="form-control" name="document_number" id="document_number" value="<?php echo e($invoice->document_number); ?>" required>
                  </div>
  
                  <div class="form-group col-md-6">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" name="document_key" id="document_key" value="<?php echo e($invoice->document_key); ?>" >
                  </div>

                  <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="<?php echo e($invoice->generatedDate()->format('d/m/Y')); ?>">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" class="form-control input-hora" name="hora" required value="<?php echo e($invoice->generatedDate()->format('g:i A')); ?>">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="<?php echo e($invoice->dueDate()->format('d/m/Y')); ?>">
                      <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
                    </div>
                  </div>
                  
                  <div class="form-group col-md-12">
                      <label for="payment_type">Actividad Comercial</label>
                      <div class="input-group">
                          <select id="commercial_activity" name="commercial_activity" class="form-control" required>
                              <?php $__currentLoopData = $arrayActividades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actividad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <option <?php echo e($invoice->commercial_activity == $actividad->codigo ? 'selected' : ''); ?> value="<?php echo e($actividad->codigo); ?>" ><?php echo e($actividad->codigo); ?> - <?php echo e($actividad->actividad); ?></option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>
                  </div>

                  <div class="form-group col-md-6">
                  <label for="sale_condition">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" name="sale_condition" class="form-control" required>
                      <option <?php echo e($invoice->sale_condition == '01' ? 'selected' : ''); ?> value="01">Contado</option>
                      <option <?php echo e($invoice->sale_condition == '02' ? 'selected' : ''); ?> value="02">Crédito</option>
                      <option <?php echo e($invoice->sale_condition == '03' ? 'selected' : ''); ?> value="03">Consignación</option>
                      <option <?php echo e($invoice->sale_condition == '04' ? 'selected' : ''); ?> value="04">Apartado</option>
                      <option <?php echo e($invoice->sale_condition == '05' ? 'selected' : ''); ?> value="05">Arrendamiento con opción de compra</option>
                      <option <?php echo e($invoice->sale_condition == '06' ? 'selected' : ''); ?> value="06">Arrendamiento en función financiera</option>
                      <option <?php echo e($invoice->sale_condition == '99' ? 'selected' : ''); ?> value="99">Otros</option>
                    </select>
                  </div>
                </div>
  
                <div class="form-group col-md-6">
                  <label for="payment_type">Método de pago</label>
                  <div class="input-group">
                    <select id="medio_pago" name="payment_type" class="form-control" onchange="toggleRetencion();" required>
                      <option <?php echo e($invoice->payment_type == '01' ? 'selected' : ''); ?> value="01" selected>Efectivo</option>
                      <option <?php echo e($invoice->payment_type == '02' ? 'selected' : ''); ?> value="02">Tarjeta</option>
                      <option <?php echo e($invoice->payment_type == '03' ? 'selected' : ''); ?> value="03">Cheque</option>
                      <option <?php echo e($invoice->payment_type == '04' ? 'selected' : ''); ?> value="04">Transferencia-Depósito Bancario</option>
                      <option <?php echo e($invoice->payment_type == '05' ? 'selected' : ''); ?> value="05">Recaudado por terceros</option>
                      <option <?php echo e($invoice->payment_type == '99' ? 'selected' : ''); ?> value="99">Otros</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group col-md-12" id="field-retencion" style="display:none;">
                  <label for="retention_percent">Porcentaje de retención</label>
                  <div class="input-group">
                    <select id="retention_percent" name="retention_percent" class="form-control" required>
                      <option value="6" <?php echo e($invoice->retention_percent == 6 ? 'selected' : ''); ?>>6%</option>
                      <option value="3" <?php echo e($invoice->retention_percent == 3 ? 'selected' : ''); ?>>3%</option>
                      <option value="0" <?php echo e($invoice->retention_percent == 0 ? 'selected' : ''); ?>>Sin retención</option>
                    </select>
                  </div>
                </div>

                  <div class="form-group col-md-6 not-required">
                    <label for="other_reference">Referencia</label>
                    <input type="text" class="form-control" name="other_reference" id="referencia" value="<?php echo e($invoice->other_reference); ?>" >
                  </div>

                  <div class="form-group col-md-6 not-required">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" class="form-control" name="buy_order" id="orden_compra" value="<?php echo e($invoice->buy_order); ?>" >
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Notas</label>
                    <input type="text" class="form-control" name="description" id="notas"  maxlength="200" placeholder="" value="<?php echo e($invoice->description); ?>">
                  </div>

              </div>
            </div>
          </div>

          <div class="form-row" id="tabla-items-factura" >  

            <div class="form-group col-md-12">
              <h3>
                Líneas de factura
              </h3>
            </div>
  
            <div class="form-group col-md-12">
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Cant.</th>
                    <th>Unidad</th>
                    <th>Precio unitario</th>
                    <th>Tipo/Categoría IVA</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                   <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <tr class="item-tabla item-index-<?php echo e($loop->index); ?>" index="<?php echo e($loop->index); ?>" attr-num="<?php echo e($loop->index); ?>" id="item-tabla-<?php echo e($loop->index); ?>">
                      <td><span class="numero-fila"><?php echo e($loop->index+1); ?></span></td>
                      <td><?php echo e($item->code); ?></td>
                      <td><?php echo e($item->name); ?></td>
                      <td><?php echo e($item->item_count); ?></td>
                      <td><?php echo e(\App\Variables::getUnidadMedicionName($item->measure_unit)); ?></td>
                      <td><?php echo e($item->unit_price); ?> </td>
                      <td><?php echo e(\App\Variables::getTipoRepercutidoIVAName($item->iva_type)); ?> <br> - <?php echo e(@\App\ProductCategory::find($item->product_type)->name); ?> </td>
                      <td><?php echo e($item->subtotal); ?></td>
                      <td><?php echo e($item->iva_amount); ?></td>
                      <td><?php echo e($item->total); ?></td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem(<?php echo e($loop->index); ?>);"> <i class="fa fa-pencil" aria-hidden="true"></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem(<?php echo e($loop->index); ?>);' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                      </td>
                      <td class="hidden">
                        <input type="hidden" class='numero' name="items[<?php echo e($loop->index); ?>][item_number]" itemname="item_number" value="<?php echo e($loop->index+1); ?>">
                        <input type="hidden" class="item_id" name="items[<?php echo e($loop->index); ?>][id]" itemname="id" value="<?php echo e($item->id); ?>"> 
                        <input type="hidden" class='codigo' name="items[<?php echo e($loop->index); ?>][code]" itemname="code" value="<?php echo e($item->code); ?>">
                        <input type="hidden" class='nombre' name="items[<?php echo e($loop->index); ?>][name]" itemname="name" value="<?php echo e($item->name); ?>">
                        <input type="hidden" class='tipo_producto' name="items[<?php echo e($loop->index); ?>][product_type]" itemname="product_type" value="<?php echo e($item->product_type); ?>">
                        <input type="hidden" class='cantidad' name="items[<?php echo e($loop->index); ?>][item_count]" itemname="item_count" value="<?php echo e($item->item_count); ?>">
                        <input type="hidden" class='unidad_medicion' name="items[<?php echo e($loop->index); ?>][measure_unit]" itemname="measure_unit" value="<?php echo e($item->measure_unit); ?>">
                        <input type="hidden" class='precio_unitario' name="items[<?php echo e($loop->index); ?>][unit_price]" itemname="unit_price" value="<?php echo e($item->unit_price); ?>">
                        <input type="hidden" class='tipo_iva' name="items[<?php echo e($loop->index); ?>][iva_type]" itemname="iva_type" value="<?php echo e($item->iva_type); ?>">
                        <input type="hidden" class='tipo_producto' name="items[<?php echo e($loop->index); ?>][product_type]" itemname="product_type" value="<?php echo e($item->product_type); ?>">
                        <input type='hidden' class='porc_identificacion_plena' itemname="porc_identificacion_plena" value='0'>
                        <input type='hidden' class='discount_type' name='items[<?php echo e($loop->index); ?>][discount_type]' itemname="discount_type" value='<?php echo e($item->discount_type); ?>'>
                        <input type='hidden' class='discount' name='items[<?php echo e($loop->index); ?>][discount]' itemname="discount" value='<?php echo e($item->discount); ?>'>
                        <input class="subtotal" type="hidden" name="items[<?php echo e($loop->index); ?>][subtotal]" itemname="subtotal" value="<?php echo e($item->subtotal); ?>">
                        <input class="porc_iva" type="hidden" name="items[<?php echo e($loop->index); ?>][iva_percentage]" itemname="iva_percentage" value="<?php echo e($item->iva_percentage); ?>">
                        <input class="monto_iva" type="hidden" name="items[<?php echo e($loop->index); ?>][iva_amount]" itemname="iva_amount" value="<?php echo e($item->iva_amount); ?>">
                        <input class="total" type="hidden" name="items[<?php echo e($loop->index); ?>][total]" itemname="total" value="<?php echo e($item->total); ?>">
                        <input class="is_identificacion_especifica" type="hidden" name="items[<?php echo e($loop->index); ?>][is_identificacion_especifica]" itemname="is_identificacion_especifica" value="<?php echo e($item->is_identificacion_especifica); ?>">


                        <input class="typeDocument" type="hidden" name="items[<?php echo e($loop->index); ?>][typeDocument]" itemname="typeDocument" value="<?php echo e($item->exoneration_document_type); ?>">
                        <input class="nombreInstitucion" type="hidden" name="items[<?php echo e($loop->index); ?>][nombreInstitucion]" itemname="nombreInstitucion" value="<?php echo e($item->exoneration_document_number); ?>">
                        <input class="nombreInstitucion" type="hidden" name="items[<?php echo e($loop->index); ?>][nombreInstitucion]" itemname="nombreInstitucion" value="<?php echo e($item->exoneration_company_name); ?>">
                        <input class="porcentajeExoneracion" type="hidden" name="items[<?php echo e($loop->index); ?>][porcentajeExoneracion]" itemname="porcentajeExoneracion" value="<?php echo e($item->exoneration_porcent); ?>">
                        <input class="montoExoneracion" type="hidden" name="items[<?php echo e($loop->index); ?>][montoExoneracion]" itemname="montoExoneracion" value="<?php echo e($item->exoneration_amount); ?>">
                        <input class="impuestoNeto" type="hidden" name="items[<?php echo e($loop->index); ?>][impuestoNeto]" itemname="impuestoNeto" value="<?php echo e($item->impuesto_neto); ?>">
                        <input class="exoneration_total_amount montoExoneracion" type="hidden" name="items[<?php echo e($loop->index); ?>][montoExoneracion]" itemname="exoneration_total_amount" value="<?php echo e($item->exoneration_total_amount); ?>">
                        <input class="exoneration_date" type="hidden" name="items[<?php echo e($loop->index); ?>][exoneration_date]" itemname="exoneration_date" value="<?php echo e(date('d/m/Y', strtotime($item->exoneration_date))); ?>">
                        <input class="tariff_heading" type="hidden" name="items[<?php echo e($loop->index); ?>][tariff_heading]" itemname="tariff_heading" value="<?php echo e($item->tariff_heading); ?>">
                        <input class="exoneration_total_gravado" type="hidden" name="items[<?php echo e($loop->index); ?>][exoneration_total_gravado]" itemname="exoneration_total_gravado" value="<?php echo e($item->exoneration_total_gravado); ?>">

                      </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
        
          <div class="form-row" id="tabla-otroscargos-factura" style="<?php echo e(isset($invoice->otherCharges[0]) ? '' : 'display: none;'); ?>">  

            <div class="form-group col-md-12">
              <h3>
                Otros cargos
              </h3>
            </div>
            
            <div class="form-group col-md-12" >
              <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Receptor</th>
                    <th>Detalle</th>
                    <th>Monto del cargo</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $invoice->otherCharges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <tr class="otros-tabla otros-index-<?php echo e($loop->index); ?>" index="<?php echo e($loop->index); ?>" attr-num="<?php echo e($loop->index); ?>" id="otros-tabla-<?php echo e($loop->index); ?>">
                      <td><span class="numero-fila"><?php echo e($loop->index+1); ?></span></td>
                      <td><?php echo e($item->document_type); ?></td>
                      <td><?php echo e($item->provider_id_number); ?> <?php echo e($item->provider_name); ?></td>
                      <td><?php echo e($item->description); ?></td>
                      <td><?php echo e(number_format($item->amount,2)); ?> </td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('otros-popup'); cargarFormOtros(<?php echo e($loop->index); ?>);"> <i class="fa fa-pencil" aria-hidden="true"></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarOtros(<?php echo e($loop->index); ?>);' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                      </td>
                      <td class="hidden">
                        <input type="hidden" class='otros-item_number' name="otros[<?php echo e($loop->index); ?>][item_number]" itemname="item_number" value="<?php echo e($loop->index+1); ?>">
                        <input type="hidden" class="otros_id" name="otros[<?php echo e($loop->index); ?>][id]" itemname="id" value="<?php echo e($item->id); ?>"> 
                        <input type="hidden" class="otros-document_type" name="otros[<?php echo e($loop->index); ?>][document_type]" itemname="document_type" value="<?php echo e($item->document_type); ?>"> 
                        <input type="hidden" class='otros-provider_id_number' name="otros[<?php echo e($loop->index); ?>][provider_id_number]" itemname="provider_id_number" value="<?php echo e($item->provider_id_number); ?>">
                        <input type="hidden" class='otros-provider_name' name="otros[<?php echo e($loop->index); ?>][provider_name]" itemname="provider_name" value="<?php echo e($item->provider_name); ?>">
                        <input type="hidden" class='otros-description' name="otros[<?php echo e($loop->index); ?>][description]" itemname="description" value="<?php echo e($item->description); ?>">
                        <input type="hidden" class='otros-amount' name="otros[<?php echo e($loop->index); ?>][amount]" itemname="amount" value="<?php echo e($item->amount); ?>">
                        <input type="hidden" class='otros-percentage' name="otros[<?php echo e($loop->index); ?>][percentage]" itemname="percentage" value="<?php echo e($item->percentage); ?>">
                      </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <?php echo $__env->make('Invoice.form-linea', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php echo $__env->make( 'Invoice.form-otros-cargos' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php echo $__env->make( 'Invoice.form-nuevo-cliente' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

          <div class="btn-holder hidden">
            <button id="btn-submit" type="submit" class="btn btn-primary">Guardar factura</button>
            <button type="submit" class="btn btn-primary">Enviar factura electrónica</button>
          </div>

        </form>
  </div>  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar factura</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script>
$(document).ready(function(){
  calcularTotalFactura();
  toggleRetencion();
});

function toggleRetencion() {
  var metodo = $("#medio_pago").val();
  if( metodo == '02' ){
    $("#field-retencion").show();
  }else {
    $("#field-retencion").hide();
  }
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/edit.blade.php ENDPATH**/ ?>