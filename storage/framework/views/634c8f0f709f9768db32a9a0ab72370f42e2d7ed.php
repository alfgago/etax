<div class="row form-container">
  <div class="col-md-12">
    <div class="row form-container">
      <div class="col-md-6">
        <b>Combrobante: </b><?php echo e($bill->document_number); ?> <br>
        <b>Emisor: </b><?php echo e(@$bill->provider->fullname); ?> <br>
        <b>Moneda: </b><?php echo e($bill->currency); ?> <br>
      </div>
      <div class="col-md-6">
        <b>Subtotal: </b><?php echo e(number_format( $bill->subtotal, 2 )); ?> <br>
        <b>Monto IVA: </b><?php echo e(number_format( $bill->iva_amount, 2 )); ?> <br>
        <?php if(isset($bill->total_iva_devuelto)): ?>
            <b>IVA Devuelto: </b><?php echo e(number_format( $bill->total_iva_devuelto, 2 )); ?> <br>
        <?php endif; ?>
        <b>Total: </b><?php echo e(number_format( $bill->total, 2 )); ?> 
      </div>
    </div>
    <hr>
  </div>
  
  <div class="col-md-12">
    <form method="POST" action="/facturas-recibidas/guardar-validar">
      <?php echo csrf_field(); ?>
      <?php echo method_field('post'); ?> 
      <input type="text" name="bill" id="bill" hidden value="<?php echo e(@$bill->id); ?>"/>
      <div class="form-row">
        <div class="form-group col-md-12">
            <b>Actividad Comercial:</b>
            <select class="form-control" name="actividad_comercial" id="actividad_comercial" placeholder="Seleccione una actividad Comercial" required >
                
                <?php $__currentLoopData = $commercial_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commercial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e(@$commercial->codigo); ?>"><?php echo e(@$commercial->actividad); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <option value="0">No asignar actividad comercial</option>
                
            </select>
        </div>
      </div>
      <div class="form-row">
        <table id="dataTable" class="table table-striped table-bordered validate-table" cellspacing="0" width="100%" >
          <thead class="thead-dark">
            <tr>
              <th>Nombre</th>
              <th>Cant.</th>
              <th>Unidad</th>
              <th>Precio unitario</th>
              <th>Subtotal</th>
              <th>IVA</th>
              <th>%</th>
              <th>Total</th>
              <th>Tipo IVA</th>
              <th>Categoría</th>
              <th>Identificación plena</th>
            </tr>
          </thead>
          <tbody>
             <tr>
               <th colspan="8">Selección masiva: </th>
               <td>
                  <div class="input-validate-iva">
                    <select class="form-control iva_type_all"  placeholder="Seleccione un código eTax" id="iva_type_all"  >
                      <option value="0">-- Seleccione --</option>
                      <?php
                        $preselectos = array();
                        foreach($company->soportados as $soportado){
                          $preselectos[] = $soportado->id;
                        }
                      ?>
                      <?php if(@$company->soportados[0]->id): ?>
                        <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="all_tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>"  is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>"><?php echo e($tipo['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <option class="all_mostrarTodos" value="1">Mostrar Todos</option>
                      <?php else: ?>
                        <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="all_tipo_iva_select"  is_identificacion_plena="<?php echo e($tipo['is_identificacion_plena']); ?>"><?php echo e($tipo['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                    </select>
                  </div>
               </td>
               <td>
                 <div class="input-validate-iva">
                   <select class="form-control product_type_all"  placeholder="Seleccione una categoría de hacienda" >
                      <option value="0">-- Seleccione --</option>
                      <?php $__currentLoopData = $categoria_productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <option value="<?php echo e(@$cat->id); ?>" codigo="<?php echo e(@$cat->bill_iva_code); ?>" posibles="<?php echo e(@$cat->open_codes); ?>" ><?php echo e(@$cat->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
               </td>
               <td>
                 <div class="input-validate-iva">
                    <select class="form-control porc_identificacion_plena_all"  >
                      <option value="0">-- Seleccione --</option>
                      <option value="13" >13%</option>
                      <option value="1" >1%</option>
                      <option value="2" >2%</option>
                      <option value="4" >4%</option>
                      <option value="5" >0% con derecho a crédito</option>
                    </select>
                  </div>
               </td>
             </tr>
             <?php $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <tr class="item-tabla item-index-<?php echo e($loop->index); ?>" index="<?php echo e($loop->index); ?>" attr-num="<?php echo e($loop->index); ?>" id="item-tabla-<?php echo e($loop->index); ?>">
                <td>
                  <input type="hidden" name="items[<?php echo e($loop->index); ?>][id]" value="<?php echo e(@$item->id); ?>">
                  <?php echo e($item->name); ?>

                </td>
                <td><?php echo e($item->item_count); ?></td>
                <td><?php echo e(\App\Variables::getUnidadMedicionName($item->measure_unit)); ?></td>
                <td><?php echo e(number_format($item->unit_price,2)); ?> </td>
                <td><?php echo e(number_format($item->subtotal,2)); ?></td>
                <td><?php echo e(number_format($item->iva_amount,2)); ?></td>
                <td><?php echo e(number_format($item->iva_percentage,0)); ?>%</td>
                <td><?php echo e(number_format($item->total,2)); ?> </td>
                <td>
                  <div class="input-validate-iva">
                    <select class="form-control iva_type" name="items[<?php echo e($loop->index); ?>][iva_type]" placeholder="Seleccione un código eTax" required >
                        <?php if(@$company->soportados[0]->id): ?>
                        <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden' : ''); ?>" identificacion="<?php echo e($tipo['is_identificacion_plena']); ?>" <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?>><?php echo e($tipo['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <option class="mostrarTodos" value="1">Mostrar Todos</option>
                      <?php else: ?>
                        <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select"  identificacion="<?php echo e($tipo['is_identificacion_plena']); ?>" <?php echo e($item->iva_type == $tipo->code ? 'selected' : ''); ?> ><?php echo e($tipo['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-validate-iva">
                    <select curr="<?php echo e($item->product_type); ?>" class="form-control product_type" name="items[<?php echo e($loop->index); ?>][product_type]" placeholder="Seleccione una categoría de hacienda" required>
                        <?php $__currentLoopData = $categoria_productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(@$cat->id); ?>" codigo="<?php echo e(@$cat->bill_iva_code); ?>" posibles="<?php echo e(@$cat->open_codes); ?>" <?php echo e($item->product_type == @$cat->id ? 'selected' : ''); ?>><?php echo e(@$cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-validate-iva">
                    <select class="form-control porc_identificacion_plena" name="items[<?php echo e($loop->index); ?>][porc_identificacion_plena]" >
                        <option value="13" <?php echo e($item->porc_identificacion_plena == 13 ? 'selected' : ''); ?>>13%</option>
                        <option value="1" <?php echo e($item->porc_identificacion_plena == 1 ? 'selected' : ''); ?>>1%</option>
                        <option value="2" <?php echo e($item->porc_identificacion_plena == 2 ? 'selected' : ''); ?>>2%</option>
                        <option value="4" <?php echo e($item->porc_identificacion_plena == 4 ? 'selected' : ''); ?>>4%</option>
                        <option value="5" <?php echo e($item->porc_identificacion_plena == 5 ? 'selected' : ''); ?>>0% con derecho a crédito</option>
                    </select>
                  </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>                      
      <button id="btn-submit" type="submit" class="btn btn-primary">Confirmar validación</button>
    </form>
  </div>
</div>

<script>
  
$(document).ready(function(){

    $(".product_type_all").change(function(){
        var product_type  = $(this).val(); 
        if(product_type != 0){
        $(".product_type").val(product_type);
      }
    });
    $(".iva_type_all").change(function(){
        var iva_type  = $(this).val(); 
        if(iva_type != 0 && iva_type != 1){
          $(".iva_type").val(iva_type);
        }
    });
    $(".porc_identificacion_plena_all").change(function(){
        var porc_identificacion_plena  = $(this).val(); 
        if(porc_identificacion_plena != 0){
          $(".porc_identificacion_plena").val(porc_identificacion_plena);
        }
    });

    $(".iva_type").change(function(){
      var codigoIVA = $(this).find(':selected').val();
      var parent = $(this).parents('tr');
      parent.find('.product_type option').hide();
      var tipoProducto = 0;
      parent.find(".product_type option").each(function(){
        var posibles = $(this).attr('posibles').split(",");
      	if(posibles.includes(codigoIVA)){
          $(this).show();
          if( !tipoProducto ){
            tipoProducto = $(this).val();
          }
        }
      });
      parent.find('.product_type').val( tipoProducto ).change();
          
      var identificacion = $(this).find(':selected').attr('identificacion');
      if(identificacion == 1){
          parent.find(".porc_identificacion_plena").removeClass("hidden");
          parent.find(".porc_identificacion_plena").attr("required");
      }else{
          parent.find(".porc_identificacion_plena").addClass("hidden");
          parent.find(".porc_identificacion_plena").removeAttr("required");
      }
    });
    
    <?php if( !$bill->is_code_validated ){ ?>
      $(".iva_type").change();
    <?php }else{ ?>
      $('.iva_type').change();
      $(".product_type").each(function(){
        $(this).val($(this).attr('curr')).change();
      });
    <?php } ?>
});
      

$( document ).ready(function() {
      $('.iva_type_all').on('change', function(e){
        if($('.iva_type_all').val() == 1){
           $.each($('.all_tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $.each($('.tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.all_mostrarTodos').addClass("hidden");
           $('.mostrarTodos').addClass("hidden");
           $('.iva_type_all').val(0);
        }
      });
    }); 

    $( document ).ready(function() {
      $('.iva_type').on('change', function(e){
        if($('.iva_type').val() == 1){
           $.each($('.tipo_iva_select'), function (index, value) {
            $(value).removeClass("hidden");
          })
           $('.mostrarTodos').addClass("hidden");
           
        }
      });
    }); 

</script><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/validar.blade.php ENDPATH**/ ?>