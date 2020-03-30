<div class="btn-group" role="group">
  <div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Acciones
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
      <?php if($data->hacienda_status == '99'): ?>
        <a href="/facturas-emitidas/editar-factura/<?php echo e($data->id); ?>" title="Ver detalle de factura" class="text-info mr-2 dropdown-item">
          <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
        </a>
        <a href="/facturas-emitidas/download-pdf/<?php echo e($data->id); ?>" title="Descargar PDF" class="text-warning mr-2 dropdown-item" download > 
          <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
        </a>
        <form id="delete-form-<?php echo e($data->id); ?>" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/eliminar-programada/<?php echo e($data->id); ?>" >
          <?php echo csrf_field(); ?>
          <?php echo method_field('delete'); ?>
          <a type="button" class="text-danger mr-2" title="Eliminar recurrente" onclick="confirmDeleteProgramada(<?php echo e($data->id); ?>);">
            <i class="fa fa-trash" aria-hidden="true"></i> <span class="toggle-item-text">Eliminar recurrente</span>
          </a>
        </form>
      <?php else: ?>
        <?php if( !$data->trashed()  ): ?>
          <?php if( !@$data->hide_from_taxes ): ?>
          
            <?php if( @$oficialHacienda ): ?>
                <a href="/facturas-emitidas/<?php echo e($data->id); ?>" title="Ver detalle de factura" class="text-info mr-2 dropdown-item">
                    <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
                </a>
                <?php if(@$company->use_invoicing ): ?>
                  <?php if(in_array($data->document_type, ['01', '08', '09', '04', '03']) && $data->hacienda_status == '03'): ?>
                  <a href="/facturas-emitidas/nota-debito/<?php echo e($data->id); ?>" title="Crear nota de debito" class="text-warning mr-2 dropdown-item">
                      <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Crear nota de debito</span>
                  </a>
                  <?php endif; ?>
                  <?php if(in_array($data->document_type, ['01', '08', '09', '04']) && $data->hacienda_status == '03'): ?>
                  <a href="/facturas-emitidas/nota-credito/<?php echo e($data->id); ?>" title="Crear nota de credito" class="text-warning mr-2 dropdown-item">
                    <i class="fa fa-ban" aria-hidden="true"></i> <span class="toggle-item-text">Crear nota de credito</span>
                  </a>
                  <?php endif; ?>
                <?php endif; ?>
                <a href="/facturas-emitidas/download-pdf/<?php echo e($data->id); ?>" title="Descargar PDF" class="text-warning mr-2 dropdown-item" download > 
                  <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span class="toggle-item-text">Descargar PDF</span>
                </a>
                <a href="/facturas-emitidas/download-xml/<?php echo e($data->id); ?>" title="Descargar XML" class="text-info mr-2 dropdown-item"> 
                  <i class="fa fa-file-text-o" aria-hidden="true"></i> <span class="toggle-item-text">Descargar XML</span>
                </a>
                <a href="/facturas-emitidas/consult/<?php echo e($data->id); ?>" title="Descargar XML Respuesta Hacienda" class="text-info mr-2 dropdown-item">
                  <i class="fa fa-file-text-o text-success" aria-hidden="true"></i> <span class="toggle-item-text">Descargar Respuesta Hacienda</span>
                </a>
              
                <a href="/facturas-emitidas/reenviar-email/<?php echo e($data->id); ?>" title="Reenviar correo electrónico" class="text-dark mr-2 dropdown-item"> 
                  <i class="fa fa-share" aria-hidden="true"></i> <span class="toggle-item-text">Reenviar correo electrónico</span>
                </a>
               
            
            <?php else: ?>
              <a href="/facturas-emitidas/<?php echo e($data->id); ?>/edit" title="Editar factura" class="text-success mr-2 dropdown-item"> 
                <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Editar factura</span>
              </a>
              <form id="delete-form-<?php echo e($data->id); ?>" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/<?php echo e($data->id); ?>" >
                <?php echo csrf_field(); ?>
                <?php echo method_field('delete'); ?>
                <a type="button" class="text-danger mr-2" title="Eliminar factura" onclick="confirmDelete(<?php echo e($data->id); ?>);">
                  <i class="fa fa-trash-o" aria-hidden="true"></i> <span class="toggle-item-text">Eliminar factura</span>
                </a>
              </form>
            <?php endif; ?>
          
            <form id="hidefromtaxes-form-<?php echo e($data->id); ?>" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/switch-ocultar/<?php echo e($data->id); ?>" >
              <?php echo csrf_field(); ?>
              <?php echo method_field('patch'); ?>
              <input type="hidden" name="hide_from_taxes" value="1">
              <a type="button" class="text-info mr-2" title="Ocultar de cálculo de impuestos" onclick="confirmHideFromTaxes(<?php echo e($data->id); ?>);">
                 <i style="color:#999;" class="fa fa-eye-slash" aria-hidden="true"></i> <span class="toggle-item-text">Ocultar de cálculos</span>
              </a>
            </form>
          <?php else: ?>
            <form id="hidefromtaxes-form-<?php echo e($data->id); ?>" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/switch-ocultar/<?php echo e($data->id); ?>" >
              <?php echo csrf_field(); ?>
              <?php echo method_field('patch'); ?>
              <a type="button" class="text-info mr-2" title="Incluir en cálculo de impuestos" onclick="confirmHideFromTaxes(<?php echo e($data->id); ?>);">
                 <i style="color:#999;" class="fa fa-eye" aria-hidden="true"></i> <span class="toggle-item-text">Incluir en cálculos</span>
              </a>
            </form>
          <?php endif; ?>
      
        <?php else: ?>
      
          <form id="recover-form-<?php echo e($data->id); ?>" class="inline-form dropdown-item" method="POST" action="/facturas-emitidas/<?php echo e($data->id); ?>/restore" >
            <?php echo csrf_field(); ?>
            <?php echo method_field('patch'); ?>
            <a type="button" class="text-success mr-2" title="Restaurar factura" onclick="confirmRecover(<?php echo e($data->id); ?>);">
              <i class="fa fa-refresh" aria-hidden="true"></i> <span class="toggle-item-text">Restaurar factura</span>
            </a>
          </form>
      
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Invoice/ext/actions.blade.php ENDPATH**/ ?>