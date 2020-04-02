<?php $__env->startSection('title'); ?> 
    Mapeo de Variables - QuickBooks
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-xl-9 col-lg-12 col-md-12">
      <?php 
        $company = $qb->company; 
        $qbConditions = $qb->conditions_json;
        $qbMethods = $qb->payment_methods_json;
        if( isset($qb->taxes_json) ) {
          $qbTipoIva = $qb->taxes_json['tipo_iva'];
          $qbTipoProducto = $qb->taxes_json['tipo_producto'];
        }
      ?>
      <form method="POST" action="/quickbooks/guardar-variables/<?php echo e($qb->id); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-row">
          <div class="form-group col-md-12">
            <h3>
              Condiciones de venta
            </h3>
          </div>
          
          <div class="form-group col-md-12">
            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
              <thead class="thead-dark">
                <tr>
                  <th>QuickBooks</th>
                  <th>eTax</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-tabla item-index-0">
                  <?php 
                    $selectedCondition = @$qbConditions['default'];
                  ?>
                  <td>Valor por defecto</td>
                  <td>
                    <select id="condicion_venta" name="sale_condition[default]" class="form-control" required>
                        <option value="01" <?php echo e($selectedCondition == "01" ? 'selected' : ''); ?>>Contado</option>
                        <option value="02" <?php echo e($selectedCondition == "02" ? 'selected' : ''); ?>>Crédito</option>
                        <option value="03" <?php echo e($selectedCondition == "03" ? 'selected' : ''); ?>>Consignación</option>
                        <option value="04" <?php echo e($selectedCondition == "04" ? 'selected' : ''); ?>>Apartado</option>
                        <option value="05" <?php echo e($selectedCondition == "05" ? 'selected' : ''); ?>>Arrendamiento con opción de compra</option>
                        <option value="06" <?php echo e($selectedCondition == "06" ? 'selected' : ''); ?>>Arrendamiento en función financiera</option>
                        <option value="99" <?php echo e($selectedCondition == "99" ? 'selected' : ''); ?>>Otros</option>
                    </select>
                  </td>
                </tr>
                <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr class="item-tabla item-index-<?php echo e($loop->index); ?>">
                    <?php 
                      $condId = $term->Id;
                      $selectedCondition = @$qbConditions[$condId];
                    ?>
                    <td><?php echo e($term->Name); ?></td>
                    <td>
                      <select id="condicion_venta" name="sale_condition[<?php echo e($term->Id); ?>]" class="form-control" required> 
                        <option value="01" <?php echo e($selectedCondition == "01" ? 'selected' : ''); ?>>Contado</option>
                        <option value="02" <?php echo e($selectedCondition == "02" ? 'selected' : ''); ?>>Crédito</option>
                        <option value="03" <?php echo e($selectedCondition == "03" ? 'selected' : ''); ?>>Consignación</option>
                        <option value="04" <?php echo e($selectedCondition == "04" ? 'selected' : ''); ?>>Apartado</option>
                        <option value="05" <?php echo e($selectedCondition == "05" ? 'selected' : ''); ?>>Arrendamiento con opción de compra</option>
                        <option value="06" <?php echo e($selectedCondition == "06" ? 'selected' : ''); ?>>Arrendamiento en función financiera</option>
                        <option value="99" <?php echo e($selectedCondition == "99" ? 'selected' : ''); ?>>Otros</option>
                      </select>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
          <div class="form-group col-md-12">
            <h3>
              Métodos de pago
            </h3>
          </div>
          
          <div class="form-group col-md-12">
            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
              <thead class="thead-dark">
                <tr>
                  <th>QuickBooks</th>
                  <th>eTax</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-tabla item-index-0">
                  <td>Valor por defecto</td>
                  <?php 
                    $selectedCondition = @$qbMethods['default'];
                  ?>
                  <td>
                    <select id="medio_pago" name="payment_type[default]" class="form-control" required>
                      <option value="01" <?php echo e($selectedCondition == "01" ? 'selected' : ''); ?>>Efectivo</option>
                      <option value="02" <?php echo e($selectedCondition == "02" ? 'selected' : ''); ?>>Tarjeta</option>
                      <option value="03" <?php echo e($selectedCondition == "03" ? 'selected' : ''); ?>>Cheque</option>
                      <option value="04" <?php echo e($selectedCondition == "04" ? 'selected' : ''); ?>>Transferencia-Depósito Bancario</option>
                      <option value="05" <?php echo e($selectedCondition == "05" ? 'selected' : ''); ?>>Recaudado por terceros</option>
                      <option value="99" <?php echo e($selectedCondition == "99" ? 'selected' : ''); ?>>Otros</option>
                    </select>
                  </td>
                </tr>
                <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr class="item-tabla item-index-<?php echo e($loop->index); ?>">
                    <?php 
                      $condId = $method->Id;
                      $selectedCondition = @$qbMethods[$condId];
                    ?>
                    <td><?php echo e($method->Name); ?></td>
                    <td>
                      <select id="medio_pago" name="payment_type[<?php echo e($method->Id); ?>]" class="form-control" required>
                        <option value="01" <?php echo e($selectedCondition == "01" ? 'selected' : ''); ?>>Efectivo</option>
                        <option value="02" <?php echo e($selectedCondition == "02" ? 'selected' : ''); ?>>Tarjeta</option>
                        <option value="03" <?php echo e($selectedCondition == "03" ? 'selected' : ''); ?>>Cheque</option>
                        <option value="04" <?php echo e($selectedCondition == "04" ? 'selected' : ''); ?>>Transferencia-Depósito Bancario</option>
                        <option value="05" <?php echo e($selectedCondition == "05" ? 'selected' : ''); ?>>Recaudado por terceros</option>
                        <option value="99" <?php echo e($selectedCondition == "99" ? 'selected' : ''); ?>>Otros</option>
                      </select>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
          
          <div class="form-group col-md-12">
            <h3>
              Códigos de impuesto
            </h3>
          </div>
          
          <div class="form-group col-md-12">
            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
              <thead class="thead-dark">
                <tr>
                  <th>QuickBooks</th>
                  <th>Código eTax</th>
                  <th>Categoría Hacienda</th>
                </tr>
              </thead>
              <tbody>
                <tr class="item-tabla item-index-0">
                  <td>Valor por defecto</td>
                  <td>
                    <select class="form-control select-search tipo_iva" name="tipo_iva[default]" id="tipo_iva">
                      <?php
                        $selectedTipoIva = @$qbTipoIva['default'];
                        $selectedProdType = @$qbTipoProducto['default'];
                      ?>
                      <?php if(@$company->repercutidos[0]->id): ?>
                        <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select" 
                             <?php echo e($selectedTipoIva == $tipo['code'] ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php else: ?>
                        <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <?php if(@$document_type == '09'): ?>
                            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e($tipo['code'] !== 'B150' ? 'hidden' : ''); ?>"
                            <?php echo e($tipo['code'] == $selectedTipoIva ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                         <?php else: ?>
                            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"
                            <?php echo e($tipo['code'] == $selectedTipoIva ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                         <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                    </select>
                  </td>
                  <td>
                    <select class="form-control" id="tipo_producto" name="tipo_producto[default]" >
                      <?php $__currentLoopData = \App\ProductCategory::whereNotNull('invoice_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['invoice_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" 
                        <?php echo e($selectedProdType == $tipo['id'] ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </td>
                </tr>
                <?php $__currentLoopData = $taxRates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr class="item-tabla item-index-<?php echo e($loop->index); ?>">
                      <?php
                        $condId = $tax->Id;
                        $selectedTipoIva = @$qbTipoIva[$condId];
                        $selectedProdType = @$qbTipoProducto[$condId];
                      ?>
                    <td><?php echo e($tax->Name); ?></td>
                    <td>
                      <select class="form-control select-search tipo_iva" id="tipo_iva" name="tipo_iva[<?php echo e($condId); ?>]">
                        <?php if(@$company->repercutidos[0]->id): ?>
                          <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  
                              <?php echo e($selectedTipoIva == $tipo['code'] ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                          <?php $__currentLoopData = \App\CodigoIvaRepercutido::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           <?php if(@$document_type == '09'): ?>
                              <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e($tipo['code'] !== 'B150' ? 'hidden' : ''); ?>" 
                              <?php echo e($tipo['code'] == $selectedTipoIva ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                           <?php else: ?>
                              <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select" 
                              <?php echo e($tipo['code'] == $selectedTipoIva ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                           <?php endif; ?>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                      </select>
                    </td>
                    <td>
                      <select class="form-control" id="tipo_producto" name="tipo_producto[<?php echo e($tax->Id); ?>]" >
                        <?php $__currentLoopData = \App\ProductCategory::whereNotNull('invoice_iva_code')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($tipo['id']); ?>" codigo="<?php echo e($tipo['invoice_iva_code']); ?>" posibles="<?php echo e($tipo['open_codes']); ?>" 
                          <?php echo e($selectedProdType == $tipo['id'] ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>

        <button id="btn-submit" class="btn btn-primary" type="submit" class="">Guardar variables</button>
        
      </form> 
  </div>  
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar variables</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('footer-scripts'); ?>
<script>
    $( document ).ready(function() {
      $('#tipo_iva').on('select2:selecting', function(e){
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Quickbooks/variable-map.blade.php ENDPATH**/ ?>