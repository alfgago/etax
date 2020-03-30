

<?php $__currentLoopData = notifications(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="#" onclick="verNotificacion(<?php echo e($notification->id); ?>);" class="a-notificacion-<?php echo e($notification->id); ?> ">
        <div class="col-md-12 div-notificaciones ">
            <span class="titulo-notificaciones"><?php echo $notification->notification->icon(); ?> <?php echo e($notification->notification->title); ?></span></br>
            <span class="date-notificaciones"><?php echo e($notification->notification->date); ?></span></br>
        </div>
    </a> 
    <hr class="divicion-notificaciones">
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/users/notificaciones-header.blade.php ENDPATH**/ ?>