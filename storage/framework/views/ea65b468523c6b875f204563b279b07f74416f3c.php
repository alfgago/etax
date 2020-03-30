<div class="main-header">
    <div class="logo">
        <a title="Volver al escritorio" href="/"><img src="<?php echo e(asset('assets/images/logo-final-150.png')); ?>" class="logo-img"></a>
    </div>
    <?php if( getCurrentSubscription()->status == 4 ): ?>
    <div class="comprar-ahora">
        <p class="description">Periodo de uso gratuito</p>
        <a class="btn btn-primary btn-buynow" href="/elegir-plan" title="Comprar ahora">Comprar ahora</a>
    </div>
    <?php endif; ?>
    <div class="menu-toggle">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div style="margin: auto"></div>

    <div class="header-part-right">
        
        <?php if( !empty( auth()->user()->teams )  && !in_array(8, auth()->user()->permisos())): ?>
            <div class="companyParent">
                <label for="country">Empresa actual:</label>
                <div class="form-group">
                    <select class="form-control select-search" id="company_change" onchange="companyChange(true);">

                        <?php $__currentLoopData = auth()->user()->teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php  
                                  $c = $row->company;
                                  if($c) { 
                                    //if($c->status == 1){
                                    $name = isset($c->name) ? $c->name.' '.$c->last_name.' '.$c->last_name2 : '-- Nueva Empresa --';  ?> 
                                    <option value="<?php echo e($c->id); ?>" <?php echo e($c->id == currentCompany() ? 'selected' : ''); ?> > <?php echo e($name); ?> </option>
                            <?php   //} 
                                  } ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <div class="notificaciones-header"> 
            <a href="#" id="notificacionesDropdown">
                <span class="fa fa-bell-o" aria-hidden="true"></span>
                <span class="notification-count <?php if(notification_count() != 0): ?> mostrar-count-notificacion <?php endif; ?> "><?php echo e(notification_count()); ?></span>
            </a>
            <div class="dropdown-notificaciones dropdown-notificaciones-right" >
                <h3 class="text-center">Notificaciones</h3>
                <span class="cerrar-notificaciones" title="Cerrar notificaciones"><i class="fa fa-times" aria-hidden="true"></i></span>
                <span class="limpiar-notificaciones" title="Marcas como leidos"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></span>
                <div id="imprimir-notificaciones" ></div>
                <div class="ver-mas-notificaciones" onclick="verNotificacion(0);">
                    Ver todas
                </div>
            </div>
        </div>
        <!-- User avatar dropdown -->
        <div class="dropdown">
            <div  class="user col align-self-end">
                <img src="<?php echo e(asset('assets/images/config-2.png')); ?>" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <i class="i-Lock-User mr-1"></i> <?php echo e(Auth::user()->first_name.' '.Auth::user()->last_name.' '.Auth::user()->last_name2); ?>

                    </div>
                    <?php 
                    $menu = new App\Menu;
                    $items = $menu->menu('menu_dropdown_header');
                    foreach ($items as $item) { ?>
                        <a class="dropdown-item" <?php echo e($item->type); ?>="<?php echo e($item->link); ?>"><?php echo e($item->name); ?></a>
                    <?php } ?>

                    <?php /* @if( !in_array(8, auth()->user()->permisos())) */ ?>
                        <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById(&quot;frm-logout&quot;).submit();">Cerrar sesión</a>
                    <form id="frm-logout" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo e(csrf_field()); ?> 
                        <?php echo view('honeypot::honeypotFormFields'); ?>
                    </form>
                    <?php /* @endif */ ?>
                </div>
            </div>
        </div>  
    </div>

</div>
<script>
    setInterval('contadorNotificaciones()',30000);
    $(".cerrar-notificaciones").click(function(){
        $("#notificacionesDropdown").click();
    });
    
    $(".limpiar-notificaciones").click(function(){
        var link = "/usuario/limpiar-notificaciones";
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $(".div-notificaciones").removeClass("notificacion-nueva");
                contadorNotificaciones();
                $("#notificacionesDropdown").click();
           }
      
        });
    });
    $("#notificacionesDropdown").click(function(){
        var link = "/usuario/notificaciones-nuevas";
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $(".dropdown-notificaciones").removeClass("todas-notificaciones");
              $("#imprimir-notificaciones").html(data);
           }
      
        });
        $(".dropdown-notificaciones").slideToggle("slow");
    });
    function cerrarNotificaciones(){
        $(".dropdown-notificaciones").removeClass("todas-notificaciones");
    }
    function notificacionVista(id){
        var link = "/usuario/notificaciones-vista/"+id;
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                if(data == 1){
                    $(".div-notificacion-"+id).removeClass("notificacion-nueva");
                }
           }
      
        });
    }

    function verNotificacion(id){

        var link = "/usuario/notificaciones/"+id;
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $("#imprimir-notificaciones").html(data);
                $(".dropdown-notificaciones").addClass("todas-notificaciones");
           }
      
        });
    }

    function contadorNotificaciones(){
        var link = "/usuario/notificaciones-contador";
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $(".notification-count").html(data);
                if(data == 0){
                    $(".notification-count").removeClass("mostrar-count-notificacion");
                }else{
                    $(".notification-count").addClass("mostrar-count-notificacion");
                }
            }
        });
    }

</script>

<?php /**PATH /var/www/resources/views/layouts/header-menu.blade.php ENDPATH**/ ?>