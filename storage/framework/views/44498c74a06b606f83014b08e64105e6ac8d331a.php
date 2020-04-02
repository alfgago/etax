
    <form id="accept-form-<?php echo e($bill->id); ?>" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/<?php echo e($bill->id); ?>" >
      <?php echo csrf_field(); ?>
      <?php echo method_field('patch'); ?>
      
      <?php if($bill->company_id == 3965): ?>
      <div class="input-validate-iva" style="display: inline-block;">
       <select class="form-control hidden" name="regiones[<?php echo e($bill->id); ?>]" placeholder="Seleccione la región" required style="font-size: 0.85em; padding: 5.75px !important; line-height: 1;">
          <option value="01" <?php echo e($bill->sucursal == '01' ? 'selected': ''); ?>>01 : San José</option>
          <option value="02" <?php echo e($bill->sucursal == '02' ? 'selected': ''); ?>>02 : Guápiles</option>
        </select>
      </div>
      <?php endif; ?>      
      <input type="hidden" name="autorizar" value="1">
      <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAuthorize(<?php echo e($bill->id); ?>);">
        Autorizar
      </a>
    </form>

    <form id="delete-form-<?php echo e($bill->id); ?>" class="inline-form" method="POST" action="/facturas-recibidas/confirmar-autorizacion/<?php echo e($bill->id); ?>" >
      <?php echo csrf_field(); ?>
      <?php echo method_field('patch'); ?>
      <input type="hidden" name="autorizar" value="0">
      <a href="#" title="Rezachar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDelete(<?php echo e($bill->id); ?>);">
        Rechazar
      </a>
    </form>
    
    <a href="/facturas-recibidas/download-pdf/<?php echo e($bill->id); ?>" title="Descargar PDF"class="btn btn-primary btn-agregar m-0" style="background: #d28923; border-color: #d28923; font-size: 0.85em;" download > 
      <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF
    </a><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/ext/auth-actions.blade.php ENDPATH**/ ?>