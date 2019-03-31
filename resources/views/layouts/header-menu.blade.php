    <div class="main-header">
            <div class="logo">
                <img src="{{asset('assets/images/logo-etax-color-mini.png')}}" class="logo-img">
            </div>
            <style>
            .logo {
                text-align: center;
                font-size: 30px;
                color: #787fff;
            }
            
            .main-header .logo {
                width: 150px;
                text-align: center;
                font-size: 30px;
                height: 80px;
                padding: 10px 0;
            }
            
            .main-header .logo img {
                width: auto;
                height: 100%;
                margin: auto;
                display: block;
                padding: 10px;
            }
            
            .cols-excel {
                list-style-type: upper-alpha;
                display: flex;
                flex-wrap: wrap;
                font-size: 11px;
                padding-left: 1rem;
            }
            
            .cols-excel li {
                width: 33.33%;
                padding-right: 1rem;
            }
            
            .iframe-container {
                position: relative;
                width: 1000px;
                max-width: 100%;
                padding-bottom: 1490px;
                overflow: hidden;
            }
            
            .iframe-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: 0;
                overflow: hidden;
            }

            </style>

            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div  class="user col align-self-end">
                        <img src="{{asset('assets/images/config-2.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i> Alfredo Gago Jiménez
                            </div>
                            <a class="dropdown-item">Perfil</a>
                            <a class="dropdown-item">Empresa</a>
                            <a class="dropdown-item">Plan</a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                                Cerrar sesión
                            </a>    
                            <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- header top menu end -->
