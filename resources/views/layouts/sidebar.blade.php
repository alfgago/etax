
  <div class="side-content-wrap">
      <div class="sidebar-left open" data-perfect-scrollbar data-suppress-scroll-x="true">
          <ul class="navigation-left">
            <li class="nav-item {{ request()->is('/') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/">
                    <img src="{{asset('assets/images/iconos/dashb.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('clientes/*') || request()->is('clientes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/clientes">
                    <img src="{{asset('assets/images/iconos/cliente.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Clientes</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('proveedores/*') || request()->is('proveedores') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/proveedores">
                    <img src="{{asset('assets/images/iconos/prove.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Proveedores</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('productos/*') || request()->is('productos') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/productos">
                    <img src="{{asset('assets/images/iconos/produ.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Productos</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('facturas-emitidas/*') || request()->is('facturas-emitidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-emitidas">
                    <img src="{{asset('assets/images/iconos/fact-emi.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Facturas emitidas</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-recibidas">
                    <img src="{{asset('assets/images/iconos/fact-reci.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Facturas recibidas</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('reportes/*') || request()->is('reportes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/reportes">
                    <img src="{{asset('assets/images/iconos/report.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Reportes</span>
                </a>
                <div class="triangle"></div>
            </li>
          </ul>
      </div>

      <div class="sidebar-overlay"></div>
  </div>
  <!--=============== Left side End ================-->
