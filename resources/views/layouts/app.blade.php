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
  
    <script src="{{mix('assets/js/common-bundle.js')}}"></script>
  
    @yield('header-scripts')
  
    <style>
      
      body, html {
        font-family: 'Pangram', 'sans-serif' !important;
        font-weight: 400;
      }
      
      h1, h2, h3, h4, h5, h6, .card-title, .text-title {
        font-weight: 500;
      }
      
      .ivas-table {
          font-size: 10px;
          color: #666
      }

      .ivas-table th {
          color: #4C006D;
          font-size: 11px;
          font-weight: 400;
      }

      .ivas-table th, 
      .ivas-table td {
          padding: 0.25rem;
          font-weight: 400;
      }

      .ivas-table th:first-of-type {
          text-align: left;
          max-width: 135px;
      }
      
      .card-title {
          font-size: 1.2rem;
          margin-bottom: 1.25rem;
          border-bottom: #e5e5e5 5px solid;
          padding-bottom: 1rem;
      }
      
    </style>
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

    <script src="/assets/js/ubicacion.js"></script>
    <script src="/assets/js/vendor/tagging.min.js"></script>
    <script src="{{asset('assets/js/es5/script.js')}}"></script>
  

    @yield('footer-scripts')
</body>

</html>
