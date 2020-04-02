<?php $__env->startSection('title'); ?> 
  Ver factura emitida
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb-buttons'); ?>
    <?php if($invoice->generation_method !== 'etax'): ?>
  <button type="submit" onclick="$('#btn-submit-form').click();"  class="btn btn-primary">Guardar factura</button>
    <?php endif; ?>
<?php $__env->stopSection(); ?> 
<?php $__env->startSection('content'); ?> 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/actualizar-categorias" class="show-form"> 
          <?php echo csrf_field(); ?>
          <?php echo method_field('post'); ?> 
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
                    </div>  
                    
                    <div class="form-group col-md-12 with-button">
                      <label for="cliente">Seleccione el cliente</label>
                      <select class="form-control select-search" name="client_id" id="client_id" placeholder="" required disabled>
                         <option value="<?php echo e($invoice->client_id); ?>" ><?php echo e($invoice->client_id_number); ?> - <?php echo e($invoice->client_first_name); ?></option>
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
                      <select class="form-control" name="currency" id="moneda" required disabled>
                        <option value="CRC" <?php echo e($invoice->currency == 'CRC' ? 'selected' : ''); ?>>CRC</option>
                        <option value="USD" <?php echo e($invoice->currency == 'USD' ? 'selected' : ''); ?>>USD</option>
                      </select>
                    </div>
  
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" disabled class="form-control" name="currency_rate" id="tipo_cambio" value="<?php echo e($invoice->currency_rate); ?>" required>
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
                  <input type="text" class="form-control" value="<?php echo e(number_format($invoice->subtotal, 2)); ?>" disabled name="subtotal"  required>
                </div>
      
                <div class="form-group col-md-4">
                  <label for="iva_amount">Monto IVA </label>
                  <input type="text" class="form-control" value="<?php echo e(number_format($invoice->iva_amount, 2)); ?>" disabled name="iva_amount" required>
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
                  <input type="text" class="form-control total" value="<?php echo e(number_format($invoice->total - $invoice->total_iva_devuelto, 2)); ?>" disabled name="total"  >
                </div>
      
              </div>
              

              <?php if($invoice->document_type == "03"): ?>
                  <div class="form-row">
                            <div class="form-group col-md-12">
                                <h3>
                                    Información de referencia
                                </h3>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="subtotal">Tipo</label>
                                <select name="code_note" id="code_note" class="form-control" required readonly disabled>
                                    <option value="01" <?php if($invoice->reason == "01"): ?> selected <?php endif; ?> >Anula documento de referencia</option>
                                    <option value="02" <?php if($invoice->reason == "02"): ?> selected <?php endif; ?>>Corrige texto de ocumento de referencia</option>
                                    <option value="03" <?php if($invoice->reason == "03"): ?> selected <?php endif; ?>>Corrige monto</option>
                                    <option value="04" <?php if($invoice->reason == "04"): ?> selected <?php endif; ?>>Referencia a otro documento</option>
                                    <option value="05" <?php if($invoice->reason == "05"): ?> selected <?php endif; ?>>Sustituye comprobante provisional por contigencia</option>
                                    <option value="99" <?php if($invoice->reason == "99"): ?> selected <?php endif; ?>>Otros</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="iva_amount">Razón</label>
                                <input type="text" class="form-control" name="reason" id="reason" placeholder="" readonly value="<?php echo e($invoice->reason); ?>">
                            </div>
                        </div>
              <?php endif; ?>
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
                    <input type="text" class="form-control" disabled name="document_number" id="document_number" value="<?php echo e($invoice->document_number); ?>" required>
                  </div>
  
                  <div class="form-group col-md-6">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" disabled name="document_key" id="document_key" value="<?php echo e($invoice->document_key); ?>" >
                  </div>

                  <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" disabled class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="<?php echo e($invoice->generatedDate()->format('d/m/Y')); ?>">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" disabled class="form-control input-hora" name="hora" required value="<?php echo e($invoice->generatedDate()->format('g:i A')); ?>">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" disabled class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="<?php echo e($invoice->dueDate()->format('d/m/Y')); ?>">
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
                                  <option <?php echo e($invoice->commercial_activity == $actividad->codigo ? 'selected' : ''); ?> value="<?php echo e($actividad->codigo); ?>" ><?php echo e($actividad->codigo); ?> - <?php echo e($actividad->actividad); ?> </option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>
                  </div>
                  <input type="text" value="<?php echo e($invoice->id); ?>" name="invoice_id" hidden>
                  <div class="form-group col-md-6">
                  <label for="sale_condition">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" disabled name="sale_condition" class="form-control" required>
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
                    <select id="medio_pago" disabled name="payment_type" class="form-control" onchange="toggleRetencion();" required>
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
                    <select id="retention_percent" disabled name="retention_percent" class="form-control" required>
                      <option value="6" <?php echo e($invoice->retention_percent == 6 ? 'selected' : ''); ?>>6%</option>
                      <option value="3" <?php echo e($invoice->retention_percent == 3 ? 'selected' : ''); ?>>3%</option>
                      <option value="0" <?php echo e($invoice->retention_percent == 0 ? 'selected' : ''); ?>>Sin retención</option>
                    </select>
                  </div>
                </div>

                  <div class="form-group col-md-6">
                    <label for="other_reference">Referencia</label>
                    <input type="text" disabled class="form-control" name="other_reference" id="referencia" value="<?php echo e($invoice->other_reference); ?>" >
                  </div>

                  <div class="form-group col-md-6">
                    <label for="buy_order">Orden de compra</label>
                    <input type="text" disabled class="form-control" name="buy_order" id="orden_compra" value="<?php echo e($invoice->buy_order); ?>" >
                  </div>

                  <div class="form-group col-md-12">
                    <label for="description">Notas</label>
                    <input type="text" disabled class="form-control" name="description" id="notas" placeholder="" value="<?php echo e($invoice->description); ?>">
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
                    <th>Código / Categoría</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                   <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <tr class="item-tabla item-index-<?php echo e($loop->index); ?>" index="<?php echo e($loop->index); ?>" attr-num="<?php echo e($loop->index); ?>" id="item-tabla-<?php echo e($loop->index); ?>">
                      <td><span class="numero-fila"><?php echo e($loop->index+1); ?></span>
                        <input type="hidden" class="item_id" name="items[<?php echo e($loop->index); ?>][id]" value="<?php echo e($item->id); ?>"> </td>
                      <td><?php echo e($item->code); ?></td>
                      <td><?php echo e($item->name); ?></td>
                      <td><?php echo e($item->item_count); ?></td>
                      <td><?php echo e(\App\UnidadMedicion::getUnidadMedicionName($item->measure_unit)); ?></td>
                      <td><?php echo e($item->unit_price); ?></td>
                      <td>
                        <select class="form-control tipo_iva tipo_iva_<?php echo e($loop->index+1); ?> select-search" name="items[<?php echo e($loop->index); ?>][tipo_iva]" <?php echo e($invoice->generation_method == 'etax' ? 'disabled' : ''); ?>>
                            <?php
                          $preselectos = array();
                          foreach($company->repercutidos as $repercutido){
                            $preselectos[] = $repercutido->id;
                          }
                        ?>
                        <?php if(@$company->repercutidos[0]->id): ?>
                          <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>"  <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          <option class="mostrarTodos" value="1">Mostrar Todos</option>
                        <?php else: ?>
                          <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        </select>
                        <select class="mt-2 form-control tipo_producto" curr="<?php echo e($item->product_type); ?>" numero="<?php echo e($loop->index+1); ?>" name="items[<?php echo e($loop->index); ?>][category_product]" <?php echo e($invoice->generation_method == 'etax' ? 'disabled' : ''); ?>>
                            
                            <?php $__currentLoopData = $product_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option <?php echo e($item->product_type == $tipo->id ? 'selected' : ''); ?> value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['invoice_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" ><?php echo e($tipo['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                      </td>
                      <td><?php echo e(number_format($item->subtotal, 2)); ?></td>
                      <td><?php echo e(number_format($item->iva_amount, 2)); ?></td>
                      <td><?php echo e(number_format($item->total, 2)); ?></td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem(<?php echo e($loop->index); ?>);"><i class='nav-icon i-Pen-2'></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem(<?php echo e($loop->index); ?>);' ><i class='nav-icon i-Close-Window'></i> </span> 
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
        
          <div class="btn-holder hidden">
            <button id="btn-submit-form" type="submit" class="btn btn-primary">Guardar factura</button>
          </div>

        </form>
  </div>  
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('footer-scripts'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="/assets/js/form-facturas.js?v=1"></script>

<script>
  $( document ).ready(function() {
      $('.tipo_iva').on('select2:selecting', function(e){
        var selectBox = document.getElementById("tipo_iva");
        if(e.params.args.data.id == 1){
           $.each($('.tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.mostrarTodos').addClass("hidden");
           e.preventDefault();
        }

      });

    });  



$(document).ready(function(){
  
  $(".tipo_iva").change(function(){
      var codigoIVA = $(this).find(':selected').val();
      var parent = $(this).parents('tr');
      parent.find('.tipo_producto option').hide();
      var tipoProducto = 0;
      parent.find(".tipo_producto option").each(function(){
        var posibles = $(this).attr('posibles').split(",");
      	if(posibles.includes(codigoIVA)){
          	$(this).show();
          	if( !tipoProducto ){
              tipoProducto = $(this).val();
            }
          }
      });
      parent.find('.tipo_producto').val( tipoProducto ).change();
  });
  
  toggleRetencion();
  
  $('.tipo_iva').change();
  $(".tipo_producto").each(function(){
    $(this).val($(this).attr('curr')).change();
  });
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

<style>
  
  td.acciones {
      display: none;
  }
  
  .table .thead-dark th:last-of-type {
      display: none;
  }
  

  
</style>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/show.blade.php ENDPATH**/ ?>