<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<?php $version = "6.45"; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('assets/images/favicon.ico')); ?>"/>
     <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?> | eTax</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tippy.js/3.4.1/tippy.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/hopscotch.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/hopscotch.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/sweetalert2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/toastr.min.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/perfect-scrollbar.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css" />
    <link href="https://unpkg.com/dropzone/dist/dropzone.css" rel="stylesheet"/>

    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/css/themes/eva.min.css')); ?>?v=<?php echo e($version); ?>">
    
    <script src="/assets/js/cybs_devicefingerprint.js?v=<?php echo e($version); ?>"></script>
    <script src="<?php echo e(asset('assets/js/common-bundle.js')); ?>?v=<?php echo e($version); ?>"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

    <?php echo $__env->yieldContent('header-scripts'); ?>

</head>

<body>
    <div class="app-admin-wrap page-<?php echo $__env->yieldContent('slug', 'default'); ?>">

      <?php echo $__env->make('layouts.header-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

       <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- ============ Body content start ============= -->
        <div class="main-content-wrap sidenav-open d-flex flex-column">

          <div class="breadcrumb">
              <h1><?php echo $__env->yieldContent('title'); ?></h1>
              <div class="breadcrumb-buttons">
                <?php echo $__env->yieldContent('breadcrumb-buttons'); ?>
              </div>
          </div>

          <div class="separator-breadcrumb border-top"></div>

          <?php echo $__env->yieldContent('content'); ?>

           <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <!-- ============ Body content End ============= -->
    </div>
    <!--=============== End app-admin-wrap ================-->


    <script src="/assets/js/ubicacion.js"></script>
    <script src="/assets/js/vendor/tagging.min.js"></script>
    <script src="<?php echo e(asset('assets/js/es5/script.js')); ?>"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tippy.js/3.4.1/tippy.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/assets/js/hopscotch.min.js"></script>
    <script src="/assets/js/dropzone.js"></script>

    <?php if( session()->has('message') ): ?>
        <script>
          toastr.success( "<?php echo e(session()->get('message')); ?>" );
        </script>
    <?php endif; ?>

    <?php if( session()->has('error') ): ?>
        <script>
          toastr.error( "<?php echo e(session()->get('error')); ?>" );
        </script>
    <?php endif; ?>

    <?php if(count($errors) > 0): ?>
      <script>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          toastr.error( "<?php echo e($error); ?>" );
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </script>
    <?php endif; ?>

    <?php echo $__env->yieldContent('header-scripts'); ?>

    <?php echo $__env->yieldContent('footer-scripts'); ?>

    <?php echo $__env->make( 'Bill.import' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make( 'Invoice.import' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make( 'Invoice.envio-masivo' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make( 'Client.import' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make( 'Provider.import' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make( 'Product.import' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make( 'Bill.import-accepts' , \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('layouts.helper-terms', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('layouts.bootstrap-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=UA-134999499-1"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-134999499-1');
      </script>

      <!-- Facebook Pixel Code -->
      <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window,document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');;
       fbq('init', '2079941852310831');
      fbq('track', 'PageView');
      </script>
      <noscript>
       <img height="1" width="1"
      src="https://www.facebook.com/tr?id=2079941852310831&ev=PageView
      &noscript=1"/>
      </noscript>
      <!-- End Facebook Pixel Code -->


    <?php
        date_default_timezone_set('America/Costa_Rica');
        $user = auth()->user();
        $hoy = getdate();
        if($hoy['hours'] < 21 && $hoy['hours'] > 7){ ?>
          <script>
            function initFreshChat() {
              window.fcWidget.init({
                token: "81da56c1-6534-4834-9847-c40e5b8f077c",
                host: "https://wchat.freshchat.com"
              });
              // To set unique user id in your system when it is available
              window.fcWidget.setExternalId("<?php echo e($user->id); ?>");
              // To set user name
              window.fcWidget.user.setFirstName("<?php echo e($user->first_name . ' ' . $user->last_name); ?>");
              // To set user email
              window.fcWidget.user.setEmail("<?php echo e($user->email); ?>");
              // To set user properties
              window.fcWidget.user.setProperties({
                plan: "Estate",                 // meta property 1
                status: "Active"                // meta property 2
              });
              
            }
            function initialize(i,t){var e;i.getElementById(t)?initFreshChat():((e=i.createElement("script")).id=t,e.async=!0,e.src="https://wchat.freshchat.com/js/widget.js",e.onload=initFreshChat,i.head.appendChild(e))}function initiateCall(){initialize(document,"freshchat-js-sdk")}window.addEventListener?window.addEventListener("load",initiateCall,!1):window.attachEvent("load",initiateCall,!1);
          </script>
    <?php }else{ ?>
            <button type="button" class="callnow" onclick="mailSoporte();">Correo</button>
    <?php } ?>

    <script type="text/javascript">
        $(document).keypress(
          function(event){
            if(document.activeElement.type != 'textarea'){
              if (event.which == '13') {
                console.log('Acción de \"Enter\" bloqueada por seguridad.');
                event.preventDefault();
              }
            }else{
              console.log("Allow enter for line break");
            }
        });
    
      /*
        function popupReproductor(){
          //window.open('https://www.callmyway.com/Welcome/SupportChatInfo/171479/?chat_type_id=5&contact_name=<?php echo e($user->first_name . " " . $user->last_name); ?>&contact_email=<?php echo e($user->email); ?>&contact_phone=<?php echo e($user->phone ? $user->phone : ''); ?>&contact_request=Chat de ayuda iniciado..&autoSubmit=1', 'Soporte eTax', 'height=350,width=350,resizable=0,marginwidth=0,marginheight=0,frameborder=0');
        };
      */
        function mailSoporte() {
            location.href = "mailto:soporte@etaxcr.com?subject=Solicitud de Soporte&body=Agradezco la ayuda con el siguiente requerimiento:";
        }
    </script>

</body>

</html>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/layouts/app.blade.php ENDPATH**/ ?>