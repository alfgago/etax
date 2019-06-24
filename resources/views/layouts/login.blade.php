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
      
      <script>
        
        function trackClickEvent( evento ){
          fbq('track', evento);
        }
        
      </script>  
      
      <style>
        .login-secondary-btn-cont .loginbtn-label {
    position: absolute;
    right: 100%;
    padding-right: .5rem;
    white-space: nowrap;
    font-size: 11px;
    bottom: 5px;
}

.login-secondary-btn-cont {
    position: relative;
    margin-bottom: 1rem;
}

.login-secondary-btn-cont .btn.btn-link {
    color: #15408e;
    background-color: #fff;
    border-color: #15408e;
    -webkit-transition: all .5s ease;
    transition: all .5s ease;
    font-size: 1em;
    -webkit-appearance: initial;
    -webkit-box-shadow: 0.2rem 0.2rem #d5d5d5;
    box-shadow: 0.2rem 0.2rem #d5d5d5;
    width: 100%;

}

.form-group.col-md-12.button-container.text-center {
    margin-top: 1rem;
}
      </style>
  
</body>

</html>