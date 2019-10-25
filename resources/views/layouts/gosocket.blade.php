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
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
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
        max-width: 1200px;
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
    .titulo-principal-gs{  
        color: #2f006d;
        text-transform: uppercase;
        font-weight: 700;
        font-size: 30px;

    }
    .titulo-secundario-gs{  
        color: #2f006d;
        font-weight: 500;
        font-size: 25px;
    }
    .plan-header {
      position: relative;
      background: #f1cb61;
      padding: 1rem;
      font-weight: 600;
      color: #333;
      border-radius: 15px;
      z-index: 2;
    }
    .plan-column .titulo {
      position: relative;
      text-align: center;
      font-size: 1.25rem;
    }
    .plan-column .plan-header .precio {
      text-align: center;
      font-size: 4rem;
    }
    .plan-column .plan-header .precio small {
      font-size: 0.65rem;
      width: 40px;
      display: inline-block;
    }
    .precio p {
      display: inline-block;
      font-size: 1rem;
    }
    .plan-column .titulo:after {
      content: '';
      display: block;
      position: relative;
      width: 100%;
      height: 5px;
      background: #333;
      margin: 0.75rem 0;
    }
    .plan-feature {
      font-size: 0.8rem;
      margin-bottom: 0.5rem;
    }
    .plan-feature.title {
      position: relative;
      text-align: center;
      margin-top: 1rem;
      font-weight: 600;
    }
    .plan-detail .plan-feature.title:first-of-type {
      margin: 0;
    }
    .plan-feature.title:after {
      content: '';
      display: block;
      position: relative;
      width: 100%;
      height: 5px;
      background: #999;
      margin: 0.75rem 0;
    }
    .plan-detail {
      padding: 3rem 1rem;
      margin-top: -2rem;
      border-radius: 15px;
      background: #eee;
    }

    .plan-feature span {
      font-size: 1rem;
      font-weight: 400;
      padding: 0 0.2rem;
      color: #2845a4;
    }
    .plan-feature i {
      padding-right: 0.5rem;
      color: #999;
      text-align: center;
      width: 25px;
    }
    .plan-feature i.fa-times {
      color: #a43228;
    }
    .plan-feature i.fa-check {
      color: #2845a4;
    }
    .texto-promocion {
        padding: 2rem 1rem;
        margin-top: -2rem;
        border-radius: 15px;
        background: #8e8e8e;
        color: white;
        text-align: center;
    }
    .plan-button {
      display: block;
      background: #f0c960;
      color: #1f2642 !important;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 15px;
      -webkit-box-shadow: 6px 6px #999;
      box-shadow: 6px 6px #999;
      font-size: 1.1rem;
      margin-top: 0;
      border: 0;
      cursor: pointer;
      text-align: center;
      margin-top: -2rem;
      position: relative;
      z-index: 0;
    }
    .plan-button a {
      width: 100%;
      display: inline-block;
    }
    .plan-button a {
      color: #1f2642 !important;
    }
    .plan-column {
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        transform: scale(0.9);
    }
    .slick-active .plan-column {
        transform: scale(1);
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
  
  
  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
  
      <script>
        
   
        jQuery('.slider').slick({
          centerMode: true,
          slidesToShow: 1,
          arrows: false,
          autoplay: true,
          autoplaySpeed: 5000,
          responsive: [
            {
              breakpoint: 768,
              settings: {
                arrows: false,
                centerMode: true,
                slidesToShow: 1
              }
            },
            {
              breakpoint: 480,
              settings: {
                arrows: false,
                centerMode: true,
                slidesToShow: 1
              }
            }
          ]
        });
        
        jQuery("#facturas-emitidas").change(function(){
          console.log(jQuery(this).val());
        });
        jQuery("#facturas-recibidas").change(function(){
          console.log(jQuery(this).val());
        });
      </script>  
      
  
</body>

</html>