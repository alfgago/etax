<?php if( !$data->trashed()  ): ?> 
 
  <?php if( @$data->accept_status != 2 ): ?>
  
    <div class="btn-group" role="group">
      <div class="btn-group" role="group">
        <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Acciones
        </button>
        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
    
      <?php if( !@$data->hide_from_taxes ): ?>
          
            <?php if( @$oficialHacienda ): ?>
              <a href="/facturas-recibidas/<?php echo e($data->id); ?>" title="Ver detalle de factura" class="text-info mr-2 dropdown-item"> 
                <i class="fa fa-info" aria-hidden="true"></i> <span class="toggle-item-text">Ver detalle de factura</span>
              </a>
            <?php else: ?>
            
              <a href="/facturas-recibidas/<?php echo e($data->id); ?>/edit" title="Editar factura" class="text-success mr-2 dropdown-item"> 
                <i class="fa fa-pencil" aria-hidden="true"></i> <span class="toggle-item-text">Editar factura</span>
              </a>
              
              <form id="delete-form-<?php echo e($data->id); ?>" class="block-form dropdown-item" method="POST" action="/facturas-recibidas/<?php echo e($data->id); ?>" >
                <?php echo csrf_field(); ?>
                <?php echo method_field('delete'); ?>
                <a type="button" class="text-danger mr-2" title="Eliminar factura" onclick="confirmDelete(<?php echo e($data->id); ?>);">
                  <i class="fa fa-trash-o" aria-hidden="true"></i> <span class="toggle-item-text">Eliminar factura</span>
                </a>
              </form>
            <?php endif; ?>
            
            <?php if( false && !(@$data->accept_status == 1 && @$data->hacienda_status == '03') ): ?>
              <form id="envioaceptacion-form-<?php echo e($data->id); ?>" class="block-form hidden " method="POST" action="/facturas-recibidas/marcar-para-aceptacion/<?php echo e($data->id); ?>" >
                <?php echo csrf_field(); ?>
                <?php echo method_field('patch'); ?>
                <a type="button" class="text-info mr-2" title="Enviar a aceptación o rechazo con Hacienda" onclick="confirmEnvioAceptacion(<?php echo e($data->id); ?>);">
                   <i class="fa fa-handshake-o" aria-hidden="true"></i>
                </a>
              </form>
            <?php endif; ?>
            
            <a link="/facturas-recibidas/validar/<?php echo e($data->id); ?>" titulo="Validación Compra" class="text-success mr-2 dropdown-item" onclick="validarPopup(this);" data-toggle="modal" data-target="#modal_estandar">
              <i class="fa fa-check" aria-hidden="true"></i> <span class="toggle-item-text">Validación de códigos</span>
            </a>
            
            <a href="/facturas-recibidas/download-pdf/<?php echo e($data->id); ?>" title="Descargar PDF" class="text-warning mr-2 dropdown-item" download > 
              <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span class="toggle-item-text">Descargar PDF</span>
            </a>
            
            <a href="/facturas-recibidas/download-xml/<?php echo e($data->id); ?>" title="Descargar XML" class="text-info mr-2 dropdown-item"> 
              <i class="fa fa-file-text-o" aria-hidden="true"></i> <span class="toggle-item-text">Descargar XML</span>
            </a>
        
            <form id="hidefromtaxes-form-<?php echo e($data->id); ?>" class="block-form dropdown-item" method="POST" action="/facturas-recibidas/switch-ocultar/<?php echo e($data->id); ?>" >
              <?php echo csrf_field(); ?>
              <?php echo method_field('patch'); ?>
              <input type="hidden" name="hide_from_taxes" value="1">
              <a type="button" class="text-info mr-2" title="Ocultar de cálculo de impuestos" onclick="confirmHideFromTaxes(<?php echo e($data->id); ?>);">
                 <i style="color:#999;" class="fa fa-eye-slash" aria-hidden="true"></i> <span class="toggle-item-text">Ocultar de cálculos</span>
              </a>
            </form>
            
            <form id="accepthacienda-form-<?php echo e($data->id); ?>" class="block-form dropdown-item" method="POST" action="/facturas-recibidas/respuesta-aceptacion/<?php echo e($data->id); ?>" >
              <?php echo csrf_field(); ?>
              <?php echo method_field('patch'); ?>
              <input type="hidden" name="respuesta" value="1">
              <a type="button" title="Rechazar" class="text-success mr-2" onclick="confirmAcceptHacienda(<?php echo e($data->id); ?>);">
                <i class="fa fa-check-square" aria-hidden="true"></i> <span class="toggle-item-text">Aceptar factura</span>
              </a>
            </form>
            
            <form id="decline-form-<?php echo e($data->id); ?>" class="block-form dropdown-item" method="POST" action="/facturas-recibidas/respuesta-aceptacion/<?php echo e($data->id); ?>" >
              <?php echo csrf_field(); ?>
              <?php echo method_field('patch'); ?>
              <input type="hidden" name="respuesta" value="2">
              <a type="button" title="Rechazar" class="text-danger mr-2" onclick="confirmDecline(<?php echo e($data->id); ?>);">
                <i class="fa fa-ban" aria-hidden="true"></i> <span class="toggle-item-text">Rechazar factura</span>
              </a>
            </form>
        
      <?php else: ?>
        <form id="hidefromtaxes-form-<?php echo e($data->id); ?>" class="block-form dropdown-item" method="POST" action="/facturas-recibidas/switch-ocultar/<?php echo e($data->id); ?>" >
          <?php echo csrf_field(); ?>
          <?php echo method_field('patch'); ?>
          <a type="button" class="text-info mr-2 dropdown-item" title="Incluir en cálculo de impuestos" onclick="confirmHideFromTaxes(<?php echo e($data->id); ?>);">
             <i style="color:#999;" class="fa fa-eye" aria-hidden="true"></i> <span class="toggle-item-text">Incluir en cálculos</span>
          </a>
        </form>
      <?php endif; ?>
    
        </div>
      </div>
    </div>
  
  
  <?php else: ?>
    <form id="accept-form-<?php echo e($data->id); ?>" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/<?php echo e($data->id); ?>" >
      <?php echo csrf_field(); ?>
      <?php echo method_field('patch'); ?>
      <input type="hidden" name="respuesta" value="1">
      <a href="#" title="Aceptar" class="btn btn-primary btn-agregar m-0" style="background: #15408E; font-size: 0.85em;" onclick="confirmAccept(<?php echo e($data->id); ?>);">
        Aceptar
      </a>
    </form>
  <?php endif; ?>


  
<?php else: ?>

  <form id="recover-form-<?php echo e($data->id); ?>" class="block-form dropdown-item" method="POST" action="/facturas-recibidas/<?php echo e($data->id); ?>/restore" >
    <?php echo csrf_field(); ?>
    <?php echo method_field('patch'); ?>
    <a type="button" class="text-success mr-2" title="Restaurar factura" onclick="confirmRecover(<?php echo e($data->id); ?>);">
      <i class="fa fa-refresh" aria-hidden="true"></i> <span class="toggle-item-text">Restaurar factura</span>
    </a>
  </form>

<?php endif; ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/ext/actions.blade.php ENDPATH**/ ?>