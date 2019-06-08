<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/png" href="{{asset('assets/images/favicon.ico')}}"/>
     <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | eTax</title>
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tippy.js/3.4.1/tippy.css" />
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/toastr.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css" />
    
    <link rel="stylesheet" href="{{asset('assets/styles/css/themes/eva.min.css')}}?v=2.05">
    
    <script src="{{asset('assets/js/common-bundle.js')}}?v=2.1.1"></script>

    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=a366a538-e5db-4a02-8c91-e6d7def5fe29"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    
    <script>
      /*window.zESettings = {
       webWidget: {
         authenticate: {
           chat: {
             jwtFn: function(callback) { 
               fetch('/usuario/zendesk-jwt').then(function(res) {
                  res.text().then(function(jwt) {
                   callback(jwt);
                  });
                });
              }
            } 
          }
        }
      };*/
    </script>
  
    @yield('header-scripts')
    
</head>

<body>
    <div class="app-admin-wrap page-@yield('slug', 'default')">

      @include('layouts.header-menu')

       @include('layouts.sidebar')

        <!-- ============ Body content start ============= -->
        <div class="main-content-wrap sidenav-open d-flex flex-column">

          <div class="breadcrumb">
              <h1>@yield('title')</h1>
              <div class="breadcrumb-buttons">
                @yield('breadcrumb-buttons')
              </div>
          </div>
          
          <div class="separator-breadcrumb border-top"></div>
          
          @yield('content')

           @include('layouts.footer')
        </div>
        <!-- ============ Body content End ============= -->
    </div>
    <!--=============== End app-admin-wrap ================-->


    <script src="/assets/js/ubicacion.js"></script>
    <script src="/assets/js/vendor/tagging.min.js"></script>
    <script src="{{asset('assets/js/es5/script.js')}}"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tippy.js/3.4.1/tippy.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    
    @if( session()->has('message') )
        <script>
          toastr.success( "{{ session()->get('message') }}" );
        </script>
    @endif
    
    @if( session()->has('error') )
        <script>
          toastr.error( "{{ session()->get('error') }}" );
        </script>
    @endif
    
    @if (count($errors) > 0)
      <script>
        @foreach ($errors->all() as $error)
          toastr.error( "{{ $error }}" );
        @endforeach
      </script>
    @endif
  

    @yield('footer-scripts')
    
    @include( 'Bill.import' )
    @include( 'Invoice.import' )
    @include( 'Client.import' )
    @include( 'Provider.import' )
    
    @include('layouts.helper-terms')
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=UA-134999499-1"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-134999499-1');
      </script>
    
</body>

</html>
