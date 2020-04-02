  <form id="decline-form-<?php echo e($bill->id); ?>" class="inline-form por-etax" method="POST" action="/facturas-recibidas/respuesta-aceptacion/<?php echo e($bill->id); ?>" >
    <?php echo csrf_field(); ?>
    <?php echo method_field('patch'); ?>
    <input type="hidden" name="respuesta" value="2">
    <a href="#" title="Rechazar" class="btn btn-primary btn-agregar m-0" style="background: #d22346; border-color: #d22346; font-size: 0.85em;" onclick="confirmDecline(<?php echo e($bill->id); ?>);">
      Rechazar
    </a>
  </form>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/ext/deny-action.blade.php ENDPATH**/ ?>