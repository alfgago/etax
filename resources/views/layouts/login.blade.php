<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') | eTax </title>

  <link rel="stylesheet" href="{{mix('assets/styles/css/themes/eva.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/styles/vendor/perfect-scrollbar.css')}}"> 
  
  @yield('header-scripts')
  
  <style>
    .auth-logo img {
        width: auto;
        height: 75px;
        margin-bottom: 25px;
    }
    
    .auth-layout-wrap {
        position: relative;
        background: #fff;
        width: 100%;
        display: block;
    }
    
    .auth-layout-wrap > div {
        width: 100%;
    }
    
    .auth-top img, 
    .auth-bottom img {
        width: 100%;
    }
    
    .auth-layout-wrap .auth-content {
        position: relative;
        z-index: 9;
        min-width: 0;
        max-width: 375px;
        padding-bottom: 5%;
        padding-top: 12%;
    }
    
    .auth-layout-wrap .auth-top {
        position: absolute;
        width: 75%;
        top: 0;
        left: 0;
    }
    
    .auth-layout-wrap  .auth-bottom {
        position: absolute;
        width: 50%;
        bottom: 0;
        right: 0;
    }
    
    .btn-primary {
        margin: 0;
    }
    
    .inline-block {
        display: inline-block;
    }
        
  </style>

</head>

<body>
  <div class="auth-layout-wrap">
    
    <div class="auth-top">
       <img src="{{asset('assets/images/top-3.png')}}">
    </div>
   
    
    <div class="auth-content">
      
        <div class="auth-logo text-center mb-4">
          <img src="{{asset('assets/images/logo-final-150.png')}}" alt="">
        </div>
        @yield('content')
        
    </div>     
    
    <div class="auth-bottom">
       <img src="{{asset('assets/images/bot-3.png')}}">
    </div>
    
  </div>
  <script src="{{mix('assets/js/common-bundle.js')}}"></script>
  <script src="{{asset('assets/js/es5/script.js')}}"></script>
  @yield('footer-scripts')
</body>

</html>