<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Sistema TAX </title>
  

    <link rel="stylesheet" href="{{mix('assets/styles/css/themes/eva.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}">
    @yield('header-scripts')
</head>

<body>
    <div class="app-admin-wrap">

      @include('layouts.header-menu')

       @include('layouts.sidebar')

        <!-- ============ Body content start ============= -->
        <div class="main-content-wrap sidenav-open d-flex flex-column">

          <div class="breadcrumb">
              <h1>@yield('title')</h1>
          </div>
          
          <div class="separator-breadcrumb border-top"></div>
          
          @yield('content')

           @include('layouts.footer')
        </div>
        <!-- ============ Body content End ============= -->
    </div>
    <!--=============== End app-admin-wrap ================-->

    <!-- ============ Search UI Start ============= -->
      @include('layouts.search')
    <!-- ============ Search UI End ============= -->

    <script src="{{mix('assets/js/common-bundle.js')}}"></script>
    <script src="{{asset('assets/js/es5/script.js')}}"></script>

    @yield('footer-scripts')
</body>

</html>
