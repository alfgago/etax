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
  
  <style>
    .flex-vertical-center {
      display: inline-flex;
      height: 100%;
      align-items: center;
    }

    .button-container .btn-primary {
        margin-top: 0;
    }

    .button-container {
        margin-top:1rem;
    }
      
      a.btn.btn-link {
        font-weight: bold;
        padding: 0;
    }
  </style>

</head>

<body>
  <div class="auth-layout-wrap">
    <div class="auth-content">
      <div class="card o-hidden">
        <div class="row">
          <div class="col-md-12">
            <div class="p-4">
              <div class="auth-logo text-center mb-4">
                <img src="{{asset('assets/images/logo-color.png')}}" alt="">
              </div>
              @yield('content')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{mix('assets/js/common-bundle.js')}}"></script>
  <script src="{{asset('assets/js/es5/script.js')}}"></script>
  @yield('footer-scripts')
</body>

</html>