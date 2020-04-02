<?php $__env->startSection('title'); ?> 
  Editar factura recibida
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row form-container">
  <div class="col-md-12">
      <form method="POST" action="/facturas-recibidas/<?php echo e($bill->id); ?>">
        <?php echo method_field('patch'); ?>
        <?php echo csrf_field(); ?>

          <input type="hidden" id="current-index" value="<?php echo e(count($bill->items)); ?>">

          <input type="hidden" id="is-compra" value="1">

          <div class="form-row">
            <div class="col-md">
              <div class="form-row">
                <div class="col-md-6">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <h3>
                        Proveedor
                      </h3>
                      <div onclick="abrirPopup('nuevo-proveedor-popup');" class="btn btn-agregar btn-agregar-cliente">Nuevo proveedor</div>
                    </div>

                    <div class="form-group col-md-12 with-button">
                      <label for="provider_id">Seleccione el proveedor</label>
                      <select class="form-control select-search" name="provider_id" id="provider_id" placeholder="" required>
                        <option value='' >-- Seleccione un proveedor --</option>
                        <?php $__currentLoopData = currentCompanyModel()->providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proveedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option <?php echo e($bill->provider_id == $proveedor->id ? 'selected' : ''); ?> value="<?php echo e($proveedor->id); ?>" ><?php echo e($proveedor->id_number); ?> - <?php echo e($proveedor->first_name); ?></option>
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
                        <option value="CRC" <?php echo e($bill->currency == 'CRC' ? 'selected' : ''); ?>>CRC</option>
                        <option value="USD" <?php echo e($bill->currency == 'USD' ? 'selected' : ''); ?>>USD</option>
                      </select>
                    </div>
  
                    <div class="form-group col-md-8">
                      <label for="currency_rate">Tipo de cambio</label>
                      <input type="text" class="form-control" name="currency_rate" id="tipo_cambio" value="<?php echo e($bill->currency_rate); ?>" required>
                    </div>
                  </div>
                </div>
              </div>  
              
              
              <div class="form-row">  
              
                <div class="form-group col-md-12">
                  <h3>
                    Datos de aceptación
                  </h3>
                </div>
                
                <div class="form-group col-md-3">
                  <label for="currency">XML de factura</label>
                  <select class="form-control" name="xml_schema" id="xml_schema" required>
                    <option value="43" <?php echo e($bill->xml_schema == 43 ? 'selected' : ''); ?>>4.3</option>
                    <option value="42" <?php echo e($bill->xml_schema == 42 ? 'selected' : ''); ?>>4.2</option>
                  </select>
                </div>
                  
                <div class="form-group col-md-9">
                    <label for="activity_company_verification">Actividad Comercial</label>
                    <div class="input-group">
                      <select id="activity_company_verification" name="activity_company_verification" class="form-control" required>
                          <?php $__currentLoopData = $arrayActividades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actividad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option <?php echo e($bill->activity_company_verification == $actividad->codigo ? 'selected' : ''); ?> value="<?php echo e($actividad->codigo); ?>" ><?php echo e($actividad->codigo); ?> - <?php echo e($actividad->actividad); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </div>
                </div>
                  
                <div class="form-group col-md-12 inline-form inline-checkbox">
                  <label for="accept_status">
                    <span>¿Aceptada desde otro proveedor?</span>
                    <input type="checkbox" class="form-control" id="accept_status" name="accept_status" <?php echo e($bill->accept_status == 1 ? 'checked' : ''); ?>>
                  </label>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_condition">Condición de acceptación</label>
                    <select class="form-control" name="accept_iva_condition" id="accept_iva_condition">
                      <option value="01" <?php echo e($bill->accept_iva_condition == "01" ? 'selected' : ''); ?>>Genera crédito IVA</option>
                      <option value="02" <?php echo e($bill->accept_iva_condition == "02" ? 'selected' : ''); ?>>Genera crédito parcial del IVA</option>
                      <option value="03" <?php echo e($bill->accept_iva_condition == "03" ? 'selected' : ''); ?>>Bienes de capital</option>
                      <option value="04" <?php echo e($bill->accept_iva_condition == "04" ? 'selected' : ''); ?>>Gasto corriente (no genera IVA)</option>
                      <option value="05" <?php echo e($bill->accept_iva_condition == "05" ? 'selected' : ''); ?>>Proporcionalidad</option>
                    </select>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_acreditable">IVA acreditable</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_acreditable" name="accept_iva_acreditable" class="form-control" value="<?php echo e($bill->accept_iva_acreditable); ?>" />
                    </div>
                </div>
                              
                <div class="form-group col-md-4">
                    <label for="accept_iva_gasto">IVA al gasto</label>
                    <div class="input-group">
                      <input type="number" id="accept_iva_gasto" name="accept_iva_gasto" class="form-control" value="<?php echo e($bill->accept_iva_gasto); ?>" />
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
      
                <div class="form-group col-md-4">
                  <label for="total">Total</label>
                  <input type="text" class="form-control total" name="total" id="total" placeholder="" readonly="true" >
                </div>
                
                <div class="form-group col-md-12">
                  <div onclick="abrirPopup('linea-popup');" class="btn btn-dark btn-agregar">Agregar línea</div>
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
                    <input type="text" class="form-control" name="document_number" id="document_number" value="<?php echo e($bill->document_number); ?>" required>
                  </div>
  
                  <div class="form-group col-md-6 not-required">
                    <label for="document_key">Clave de factura</label>
                    <input type="text" class="form-control" name="document_key" id="document_key" value="<?php echo e($bill->document_key); ?>" >
                  </div>
                
                  <div class="form-group col-md-4">
                    <label for="generated_date">Fecha</label>
                    <div class='input-group date inputs-fecha'>
                        <input id="fecha_generada" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="<?php echo e($bill->generatedDate()->format('d/m/Y')); ?>">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="hora">Hora</label>
                    <div class='input-group date inputs-hora'>
                        <input id="hora" class="form-control input-hora" name="hora" required value="<?php echo e($bill->generatedDate()->format('g:i A')); ?>">
                        <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                    </div>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="due_date">Fecha de vencimiento</label>
                    <div class='input-group date inputs-fecha'>
                      <input id="fecha_vencimiento" class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="<?php echo e($bill->dueDate()->format('d/m/Y')); ?>">
                      <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                  <label for="sale_condition">Condición de venta</label>
                  <div class="input-group">
                    <select id="condicion_venta" name="sale_condition" class="form-control" required>
                      <option <?php echo e($bill->sale_condition == '01' ? 'selected' : ''); ?> value="01">Contado</option>
                      <option <?php echo e($bill->sale_condition == '02' ? 'selected' : ''); ?> value="02">Crédito</option>
                      <option <?php echo e($bill->sale_condition == '03' ? 'selected' : ''); ?> value="03">Consignación</option>
                      <option <?php echo e($bill->sale_condition == '04' ? 'selected' : ''); ?> value="04">Apartado</option>
                      <option <?php echo e($bill->sale_condition == '05' ? 'selected' : ''); ?> value="05">Arrendamiento con opción de compra</option>
                      <option <?php echo e($bill->sale_condition == '06' ? 'selected' : ''); ?> value="06">Arrendamiento en función financiera</option>
                      <option <?php echo e($bill->sale_condition == '99' ? 'selected' : ''); ?> value="99">Otros</option>
                    </select>
                  </div>
                </div>
  
                <div class="form-group col-md-6">
                  <label for="payment_type">Método de pago</label>
                  <div class="input-group">
                    <select id="medio_pago" name="payment_type" class="form-control" required>
                      <option <?php echo e($bill->payment_type == '01' ? 'selected' : ''); ?> value="01" selected>Efectivo</option>
                      <option <?php echo e($bill->payment_type == '02' ? 'selected' : ''); ?> value="02">Tarjeta</option>
                      <option <?php echo e($bill->payment_type == '03' ? 'selected' : ''); ?> value="03">Cheque</option>
                      <option <?php echo e($bill->payment_type == '04' ? 'selected' : ''); ?> value="04">Transferencia-Depósito Bancario</option>
                      <option <?php echo e($bill->payment_type == '05' ? 'selected' : ''); ?> value="05">Recaudado por terceros</option>
                      <option <?php echo e($bill->payment_type == '99' ? 'selected' : ''); ?> value="99">Otros</option>
                    </select>
                  </div>
                </div>

                <div class="form-group col-md-6 not-required">
                  <label for="other_reference">Referencia</label>
                  <input type="text" class="form-control" name="other_reference" id="referencia" value="<?php echo e($bill->other_reference); ?>" >
                </div>

                <div class="form-group col-md-6 not-required">
                  <label for="buy_order">Orden de compra</label>
                  <input type="text" class="form-control" name="buy_order" id="orden_compra" value="<?php echo e($bill->buy_order); ?>" >
                </div>

                <div class="form-group col-md-12">
                  <label for="description">Notas</label>
                  <input type="text" class="form-control" name="description" id="notas" placeholder="" value="<?php echo e($bill->description); ?>">
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
                    <th>Tipo IVA</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                   <?php $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <tr class="item-tabla item-index-<?php echo e($loop->index); ?>" index="<?php echo e($loop->index); ?>" attr-num="<?php echo e($loop->index); ?>" id="item-tabla-<?php echo e($loop->index); ?>">
                      <td><span class="numero-fila"><?php echo e($loop->index+1); ?></span></td>
                      <td><?php echo e($item->code); ?></td>
                      <td><?php echo e($item->name); ?></td>
                      <td><?php echo e($item->item_count); ?></td>
                      <td><?php echo e(\App\Variables::getUnidadMedicionName($item->measure_unit)); ?></td>
                      <td><?php echo e($item->unit_price); ?> </td>
                      <td><?php echo e(\App\Variables::getTipoSoportadoIVAName($item->iva_type)); ?> <br> - <?php echo e(@\App\ProductCategory::find($item->product_type)->name); ?> </td>
                      <td><?php echo e($item->subtotal); ?></td>
                      <td><?php echo e($item->iva_amount); ?></td>
                      <td><?php echo e($item->total); ?> </td>
                      <td class='acciones'>
                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem(<?php echo e($loop->index); ?>);"> <i class="fa fa-pencil" aria-hidden="true"></i> </span> 
                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem(<?php echo e($loop->index); ?>);' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span> 
                      </td>
                      <td class="hidden">
                        <input type="hidden" class='numero' name="items[<?php echo e($loop->index); ?>][item_number]" value="<?php echo e($loop->index+1); ?>" itemname="item_number">
                        <input type="hidden" class="item_id" name="items[<?php echo e($loop->index); ?>][id]" value="<?php echo e($item->id); ?>" itemname="id"> 
                        <input type="hidden" class='codigo' name="items[<?php echo e($loop->index); ?>][code]" value="<?php echo e($item->code); ?>" itemname="code">
                        <input type="hidden" class='nombre' name="items[<?php echo e($loop->index); ?>][name]" value="<?php echo e($item->name); ?>" itemname="name">
                        <input type="hidden" class='tipo_producto' name="items[<?php echo e($loop->index); ?>][product_type]" value="<?php echo e($item->product_type); ?>" itemname="product_type">
                        <input type="hidden" class='cantidad' name="items[<?php echo e($loop->index); ?>][item_count]" value="<?php echo e($item->item_count); ?>" itemname="item_count">
                        <input type="hidden" class='unidad_medicion' name="items[<?php echo e($loop->index); ?>][measure_unit]" value="<?php echo e($item->measure_unit); ?>" itemname="measure_unit">
                        <input type="hidden" class='precio_unitario' name="items[<?php echo e($loop->index); ?>][unit_price]" value="<?php echo e($item->unit_price); ?>" itemname="unit_price">
                        <input type="hidden" class='tipo_iva' name="items[<?php echo e($loop->index); ?>][iva_type]" value="<?php echo e($item->iva_type); ?>" itemname="iva_type">
                        <input type='hidden' class='porc_identificacion_plena' name='items[<?php echo e($loop->index); ?>][porc_identificacion_plena]' value='<?php echo e($item->porc_identificacion_plena); ?>' itemname="porc_identificacion_plena">
                        <input type='hidden' class='discount_type' name='items[<?php echo e($loop->index); ?>][discount_type]' value='<?php echo e($item->discount_type); ?>' itemname="discount_type">
                        <input type='hidden' class='discount' name='items[<?php echo e($loop->index); ?>][discount]' value='<?php echo e($item->discount); ?>' itemname="discount">
                        <input class="subtotal" type="hidden" name="items[<?php echo e($loop->index); ?>][subtotal]" value="<?php echo e($item->subtotal); ?>" itemname="subtotal">
                        <input class="porc_iva" type="hidden" name="items[<?php echo e($loop->index); ?>][iva_percentage]" value="<?php echo e($item->iva_percentage); ?>" itemname="iva_percentage">
                        <input class="monto_iva" type="hidden" name="items[<?php echo e($loop->index); ?>][iva_amount]" value="<?php echo e($item->iva_amount); ?>" itemname="iva_amount">
                        <input class="total" type="hidden" name="items[<?php echo e($loop->index); ?>][total]" value="<?php echo e($item->total); ?>" itemname="total">
                        <input class="is_identificacion_especifica" type="hidden" name="items[<?php echo e($loop->index); ?>][is_identificacion_especifica]" value="<?php echo e($item->is_identificacion_especifica); ?>" itemname="is_identificacion_especifica">
                      </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
        
        
          <div class="form-row" id="tabla-otroscargos-factura" style="display: none;">  

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
                
                </tbody>
              </table>
            </div>
          </div>
          
          <?php echo $__env->make( 'Bill.form-otros-cargos' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php echo $__env->make( 'Bill.form-linea' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php echo $__env->make( 'Bill.form-nuevo-proveedor' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
          <button id="btn-submit" type="submit" class="hidden">Guardar factura</button>

        </form>
  </div>  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar factura</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>

<script>

$(document).ready(function(){
  
  $('#tipo_iva').val('3');
  
  var subtotal = 0;
  var monto_iva = 0;
  var total = 0;
  $('.item-tabla').each(function(){
    var s = parseFloat($(this).find('.subtotal').val());
    var m = parseFloat($(this).find('.monto_iva').val());
    var t = parseFloat($(this).find('.total').val());
    subtotal += s;
    monto_iva += m;	
    total += t;	
  });
  
  $('#subtotal').val( fixComas(subtotal) );
  $('#monto_iva').val( fixComas(monto_iva) );
  $('#total').val( fixComas(total) );
  
  toggleRetencion();
  
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/edit.blade.php ENDPATH**/ ?>