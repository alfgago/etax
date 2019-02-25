
  <div class="side-content-wrap">
      <div class="sidebar-left open" data-perfect-scrollbar data-suppress-scroll-x="true">
          <ul class="navigation-left">
            <li class="nav-item {{ request()->is('/') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/">
                    <i class="nav-icon i-Bar-Chart"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('clientes/*') || request()->is('clientes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/">
                    <i class="nav-icon i-Professor"></i>
                    <span class="nav-text">Clientes</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('proveedores/*') || request()->is('proveedores') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/">
                    <i class="nav-icon i-Professor"></i>
                    <span class="nav-text">Proveedores</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('productos/*') || request()->is('productos') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/productos">
                    <i class="nav-icon i-Suitcase"></i>
                    <span class="nav-text">Productos</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('facturas-emitidas/*') || request()->is('facturas-emitidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-emitidas">
                    <i class="nav-icon i-Billing"></i>
                    <span class="nav-text">Facturas emitidas</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-recibidas">
                    <i class="nav-icon i-Billing"></i>
                    <span class="nav-text">Facturas recibidas</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item {{ request()->is('reportes/*') || request()->is('reportes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/reportes">
                    <i class="nav-icon i-Bar-Chart-2"></i>
                    <span class="nav-text">Reportes</span>
                </a>
                <div class="triangle"></div>
            </li>
          </ul>
      </div>

      <div class="sidebar-overlay"></div>
  </div>
  <!--=============== Left side End ================-->
