    <div class="main-header">
            <div class="logo">
                <img src="{{asset('assets/images/logo-etax.png')}}" class="logo-img">
            </div>
            <style>
            .logo {
                text-align: center;
                font-size: 30px;
                color: #787fff;
            }
            
            .main-header .logo img {
    width: auto;
    height: 60px;
    margin: auto;
    display: block;
    padding: 10px;
}
            </style>

            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div  class="user col align-self-end">
                        <img src="{{asset('assets/images/faces/1.jpg')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i> Timothy Carlson
                            </div>
                            <a class="dropdown-item">Account settings</a>
                            <a class="dropdown-item">Billing history</a>
                            <a class="dropdown-item" href="">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- header top menu end -->
