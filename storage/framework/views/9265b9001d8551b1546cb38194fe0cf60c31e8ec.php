<?php $__env->startComponent('mail::message', ['customImg' => @$customImg]); ?>
<p>Estimado Cliente: <b><?php echo e($data_invoice->client_first_name .' '.$data_invoice->client_last_name); ?></b><br><br>
    Adjunto a este correo encontrará un Comprobante Electrónico en formato XML y su correspondiente representación en
    formato PDF, por concepto de facturación de <b><?php echo e($company->business_name); ?></b>. Lo anterior con base en las
    especificaciones del Ministerio de Hacienda. <br><br>
    Recibirá una notificación  cuando la factura sea recibida por hacienda.
    <br><br>
    ¡Muchas Gracias!
</p>
<?php if (isset($__componentOriginal2dab26517731ed1416679a121374450d5cff5e0d)): ?>
<?php $component = $__componentOriginal2dab26517731ed1416679a121374450d5cff5e0d; ?>
<?php unset($__componentOriginal2dab26517731ed1416679a121374450d5cff5e0d); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/emails/invoice/paid.blade.php ENDPATH**/ ?>