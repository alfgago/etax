<?php $__env->startSection('title'); ?> 
  Aceptación manual de facturas 4.3
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
    <div onclick="abrirPopup('importar-aceptacion-popup');" class="btn btn-primary">Importar facturas para aceptación</div>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-md-12">
        <div class="descripcion mb-4">
          Este proceso <b style="text-decoration: underline;">NO</b> genera la aceptación o rechazo ante Hacienda. Solamente valida la información que debe llevar el mensaje receptor recibido por otros proveedores.
        </div>
          
        <table id="bill-table" class="dataTable table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Emisor</th>
              <th>Comprobante</th>
              <th>Total en <br>factura</th>
              <th>Total en <br>aceptación (₡)</th>
              <th>IVA <br>Total (₡)</th>
              <th>IVA <br>Acreditable (₡)</th>
              <th>IVA <br>Gasto (₡)</th>
              <th>F. Generada</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tbody>
            <?php if( $bills->count() ): ?>
              <?php $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr id="row-index-<?php echo e($loop->index); ?>">
                  <td><?php echo e(@$data->provider->getFullName()); ?></td>
                  <td><?php echo e($data->document_number); ?></td>
                  <td><?php echo e($data->currency); ?> <?php echo e(number_format($data->total)); ?></td>
                  <td><?php echo e(number_format($data->accept_total_factura)); ?></td>
                  <td><?php echo e(number_format($data->accept_iva_total)); ?></td>
                  <td><input style="max-width:90px;" type="number" min="0" step="0.01" class="accept_iva_acreditable-linea" value="<?php echo e(number_format($data->accept_iva_acreditable)); ?>" onkeyup="setTo('<?php echo e($loop->index); ?>', 'accept_iva_acreditable', this.value)" /></td>
                  <td><input style="max-width:90px;" type="number" min="0" step="0.01" class="accept_iva_gasto-linea" value="<?php echo e(number_format($data->accept_iva_gasto)); ?>" onkeyup="setTo('<?php echo e($loop->index); ?>', 'accept_iva_gasto', this.value)" /></td>
                  <td><?php echo e($data->generatedDate()->format('d/m/Y')); ?></td>
                  <td>
                    <form id="accept-form-<?php echo e($data->id); ?>" class="inline-form por-etax" method="POST" action="/facturas-recibidas/confirmar-aceptacion-otros/<?php echo e($data->id); ?>" >
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('patch'); ?>
                      <input type="hidden" name="respuesta" value="1">
                      <input type="hidden" required name="accept_iva_acreditable" class="accept_iva_acreditable" value="<?php echo e($data->accept_iva_acreditable); ?>">
                      <input type="hidden" required name="accept_iva_gasto" class="accept_iva_gasto" value="<?php echo e($data->accept_iva_gasto); ?>">
                      <a href="#" title="Confirmar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAccept(<?php echo e($data->id); ?>);">
                        Confirmar
                      </a>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

          </tbody>
          </tbody>
        </table>
        
        <?php echo e($bills->links()); ?>

  </div>  
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-scripts'); ?>

<script>
  
  function confirmAccept( id ) {
    
    var formId = "#accept-form-"+id;
    Swal.fire({
      title: '¿Está seguro que desea aceptar la factura?',
      text: "Al aceptarla, la factura será tomada en cuenta para el cálculo en eTax.",
      type: 'success',
      customContainerClass: 'container-success',
      showCloseButton: true,
      showCancelButton: true,
      confirmButtonText: 'Sí, quiero aceptarla'
    }).then((result) => {
      if (result.value) {
        $(formId).submit();
      }
    })
    
  }
  
  function setTo( rowIndex, field, value) {
    $('#row-index-'+rowIndex+' .'+field).val( value );
  }
  
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/index-aceptaciones-otros.blade.php ENDPATH**/ ?>