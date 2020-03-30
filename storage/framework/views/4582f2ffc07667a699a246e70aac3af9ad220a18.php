<?php $__env->startSection('title'); ?> 
  	Validación de códigos eTax en facturas recibidas
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
    <div onclick="abrirPopup('importar-recibidas-popup');" class="btn btn-primary">Importar facturas recibidas</div>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?> 
<div class="row">
  <div class="col-md-12">
          
      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Emisor</th>
              <th>Moneda</th>
              <th>Subtotal</th>
              <th>Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="4">Fecha</th>
              <th data-priority="1">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if( $bills->count() ): ?>
              <?php $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($data->document_number); ?></td>
                  <td><?php echo e(@$data->provider->fullname); ?></td>
                  <td><?php echo e($data->currency); ?></td>
                  <td class="text-right"><?php echo e(number_format( $data->subtotal, 2 )); ?></td>
                  <td class="text-right"><?php echo e(number_format( $data->iva_amount - $data->total_iva_devuelto, 2 )); ?> <small><?php echo e($data->total_iva_devuelto ? "($data->total_iva_devuelto devuelto)" : ""); ?></small></td>
                  <td class="text-right"><?php echo e(number_format( $data->total, 2 )); ?></td>
                  <td><?php echo e(@$data->generatedDate()->format('d/m/Y')); ?></td>
                  <td>
                    <a link="/facturas-recibidas/validar/<?php echo e($data->id); ?>" titulo="Verificación de compra" class="btn btn-primary m-0 verificar_compra" style="color:#fff; font-size: 0.85em;" onclick="" data-toggle="modal" data-target="#modal_estandar">Validar</a>
                    <a style="margin-left: .5rem;" href="/facturas-recibidas/download-pdf/<?php echo e($data->id); ?>" title="Descargar PDF" class="text-warning mr-2" download > 
                      <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
          </tbody>
        </table>
        <?php echo e($bills->links()); ?>

  </div>  
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-scripts'); ?>

<style>
	form.inline-form.validaciones,
	form.inline-form.validaciones .input-validate-iva, 
	form.inline-form.validaciones .input-validate-iva select {
	    width: 100%;
	}
	
	form.inline-form.validaciones button {
	    border: 1px solid;
	    margin-top: 0.5rem !important;
	}
	
	#invoice-table td small {
	  font-size: .75em !important;
	}

</style>
<script>
  $(function(){
      $(".verificar_compra").click(function(){
        var link = $(this).attr("link");
        var titulo = $(this).attr("titulo");
        $("#titulo_modal_estandar").html(titulo);
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
              
                $("#body_modal_estandar").html(data);

           }

        });
      });

  });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/index-validaciones.blade.php ENDPATH**/ ?>