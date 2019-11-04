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
    .transparencia{    
      background: rgba(255, 255, 255, 0.60);
      height: 100%;
      width: 100%;
      position: fixed;
      z-index: 9;
    }
    .slick-active .plan-column .transparencia{
        background: none;
        z-index: 0;
        height: 0%;
        width: 0%;
        position: fixed;
    }
    .dots-gs{
      text-align: center;
      position: relative;
      list-style: none;
    }
    .dots-gs li{
      display: contents;
    }
    .dots-gs li button{
      background: #8e8e8e;
      border: 0px;
      border-radius: 20px;
      height: 20px;
      width: 20px;
      font-size: 5px;
      color: transparent;
      margin: 5px;
    }
    
    input[type=range] {
      -webkit-appearance: none;
      width: 100%;
      margin: 7.5px 0;
    }
    input[type=range]:focus {
      outline: none;
    }
    input[type=range]::-webkit-slider-runnable-track {
      width: 100%;
      height: 5px;
      cursor: pointer;
      box-shadow: 0px 0px 0px rgba(217, 64, 121, 0), 0px 0px 0px rgba(221, 85, 136, 0);
      background: #d94079;
      border-radius: 10px;
      border: 1px solid #d94079;
    }
    input[type=range]::-webkit-slider-thumb {
      box-shadow: 0px 0px 0px rgba(40, 65, 112, 0), 0px 0px 0px rgba(47, 76, 131, 0);
      border: 1px solid #284170;
      height: 20px;
      width: 20px;
      border-radius: 10px;
      background: #284170;
      cursor: pointer;
      -webkit-appearance: none;
      margin-top: -8.5px;
    }
    input[type=range]:focus::-webkit-slider-runnable-track {
      background: #d94079;
    }
    input[type=range]::-moz-range-track {
      width: 100%;
      height: 5px;
      cursor: pointer;
      box-shadow: 0px 0px 0px rgba(217, 64, 121, 0), 0px 0px 0px rgba(221, 85, 136, 0);
      background: #d94079;
      border-radius: 10px;
      border: 1px solid #d94079;
    }
    input[type=range]::-moz-range-thumb {
      box-shadow: 0px 0px 0px rgba(40, 65, 112, 0), 0px 0px 0px rgba(47, 76, 131, 0);
      border: 1px solid #284170;
      height: 20px;
      width: 20px;
      border-radius: 10px;
      background: #284170;
      cursor: pointer;
    }
    input[type=range]::-ms-track {
      width: 100%;
      height: 5px;
      cursor: pointer;
      background: transparent;
      border-color: transparent;
      color: transparent;
    }
    input[type=range]::-ms-fill-lower {
      background: #d94079;
      border: 1px solid #d94079;
      border-radius: 20px;
      box-shadow: 0px 0px 0px rgba(217, 64, 121, 0), 0px 0px 0px rgba(221, 85, 136, 0);
    }
    input[type=range]::-ms-fill-upper {
      background: #d94079;
      border: 1px solid #d94079;
      border-radius: 20px;
      box-shadow: 0px 0px 0px rgba(217, 64, 121, 0), 0px 0px 0px rgba(221, 85, 136, 0);
    }
    input[type=range]::-ms-thumb {
      box-shadow: 0px 0px 0px rgba(40, 65, 112, 0), 0px 0px 0px rgba(47, 76, 131, 0);
      border: 1px solid #284170;
      height: 20px;
      width: 20px;
      border-radius: 10px;
      background: #284170;
      cursor: pointer;
      height: 5px;
    }
    input[type=range]:focus::-ms-fill-lower {
      background: #d94079;
    }
    input[type=range]:focus::-ms-fill-upper {
      background: #d94079;
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
          autoplay: false,
          autoplaySpeed: 5000,
          dots: true,
          dotsClass: 'dots-gs',
          infinite: true,
          speed: 500,
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
          var emitidas = jQuery("#facturas-emitidas").val();
          if(emitidas > 5000){
              emitidas = "+5000";
          }
          jQuery("#text-facturas-emitidas").html("<b>"+emitidas+"</b>");
          select_plan();
        });
        jQuery("#facturas-recibidas").change(function(){
          var recibidas = jQuery("#facturas-recibidas").val();
          if(recibidas > 5000){
              recibidas = "+5000";
          }
          jQuery("#text-facturas-recibidas").html("<b>"+recibidas+"</b>");
          select_plan();
        });

        function select_plan(){
          var recibidas = jQuery("#facturas-recibidas").val();
          var emitidas = jQuery("#facturas-emitidas").val();
          var plan = 7;
          if(emitidas <= 5000 ){
            plan = 6;
          }
          if(emitidas <= 2000 ){
            plan = 5;
          }
          if(emitidas <= 250 ){
            plan = 4;
          }
          if(emitidas <= 50 && recibidas <= 400 ){
            plan = 3;
          }
          if(emitidas <= 25 && recibidas <= 200 ){
            plan = 2;
          }
          if(emitidas <= 5 && recibidas <= 40 ){
            plan = 1;
          }
          jQuery('ul.dots-gs li:nth-of-type('+plan+')').click();

        }
      </script>  
      
  
</body>

</html>