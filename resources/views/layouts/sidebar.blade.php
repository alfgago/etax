
  <div class="side-content-wrap">
      <div class="sidebar-left open" >
          <ul class="navigation-left">
            <li class="nav-item {{ request()->is('/') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/">
                    <img src="{{asset('assets/images/iconos/dashb.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Escritorio</span>
                </a>
                
            </li>
            
            <li class="nav-item {{ request()->is('facturas-emitidas/*') || request()->is('facturas-emitidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-emitidas">
                    <img src="{{asset('assets/images/iconos/ventas.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Ventas</span>
                </a>
                
                <div class="subitems">
                    <a href="/facturas-emitidas">Ver todas</a>
                    <a href="/facturas-emitidas/create">Registrar factura existente</a>
                    <a href="#" onclick="abrirPopup('importar-emitidas-popup');">Importar facturas</a>
                    <a href="/facturas-emitidas/validaciones">Validar facturas</a>
                    <a href="/facturas-emitidas/autorizaciones">Autorizar facturas por email</a>
                </div>
                
            </li>
            
            <li class="nav-item {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-recibidas">
                    <img src="{{asset('assets/images/iconos/compras.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Compras</span>
                </a>
                
                <div class="subitems">
                    <a href="/facturas-recibidas">Ver todas</a>
                    <a href="/facturas-recibidas/create">Registrar factura recibida</a>
                    <a href="#" onclick="abrirPopup('importar-recibidas-popup');">Importar facturas</a>
                    <a href="/facturas-recibidas/validaciones">Validar facturas</a>
                    <a href="/facturas-recibidas/autorizaciones">Autorizar facturas por email</a>
                    <a href="/sales">Comprar productos eTax</a>
                </div>
            </li>
            
            <li class="hidden nav-item small-nav {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/facturas-recibidas">
                    <img src="{{asset('assets/images/iconos/facturacion.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Facturación</span>
                </a>
                
                
                <div class="subitems">
                    <a href="/facturas-emitidas">Ver documentos emitidos</a>
                    <a href="/facturas-emitidas/emitir-factura">Emitir factura electrónica</a>
                    <a href="/facturas-emitidas/emitir-tiquete">Emitir tiquete electrónico</a>
                    <a href="/facturas-emitidas/emitir-factura">Emitir nota de débito</a>
                    <a href="/facturas-recibidas/aceptaciones">Aceptación de facturas recibidas</a>
                </div>
            </li>
            
            <li title="Facturación deshabilitada durante la prueba gratuita" class="soon nav-item small-nav {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" >
                <a  title="Facturación deshabilitada durante la prueba gratuita" class="nav-item-hold" href="#">
                    <img src="{{asset('assets/images/iconos/facturacion.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Facturación</span>
                </a>
                
                <div class="subitems">
                    <a title="Facturación deshabilitada durante la prueba gratuita" href="#">Ver documentos emitidos</a>
                    <a title="Facturación deshabilitada durante la prueba gratuita" href="#">Emitir factura electrónica</a>
                    <a title="Facturación deshabilitada durante la prueba gratuita" href="#">Emitir tiquete electrónico</a>
                    <a title="Facturación deshabilitada durante la prueba gratuita" href="#">Emitir nota de débito</a>
                    <a title="Facturación deshabilitada durante la prueba gratuita#7" href="#">Aceptación de facturas recibidas</a>
                </div>
            </li>
            
            <style>
                li.hidden.nav-item.small-nav {
                    display: none !important;
                }
                
                li.soon.nav-item.small-nav a {
                    opacity: 0.7;
                    cursor: not-allowed;
                }
                
                li.soon.nav-item.small-nav:before {
                    background: #a3a1a7 !important;opacity: 1 !important;
                }
                
                li.soon.nav-item.small-nav .subitems a {
                    background: #787779 !important;opacity: 1 !important;
                }
            </style>
            
            <li class="nav-item small-nav {{ request()->is('reportes/*') || request()->is('reportes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/cierres">
                    <img src="{{asset('assets/images/iconos/report.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Cierres de mes</span>
                </a>
                
            </li>
            
            <li class="nav-item small-nav {{ request()->is('reportes/*') || request()->is('reportes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/reportes">
                    <img src="{{asset('assets/images/iconos/report.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Reportes</span>
                </a>
                
            </li>
            
            <li class="nav-item small-nav {{ request()->is('clientes/*') || request()->is('clientes') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/clientes">
                    <img src="{{asset('assets/images/iconos/cliente.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Clientes</span>
                </a>
                
                
                <div class="subitems">
                    <a href="/clientes">Ver todos</a>
                    <a href="/clientes/create">Crear cliente</a>
                    <a href="#" onclick="abrirPopup('importar-clientes-popup');">Importar clientes</a>
                </div>
            </li>
            <li class="nav-item small-nav {{ request()->is('proveedores/*') || request()->is('proveedores') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/proveedores">
                    <img src="{{asset('assets/images/iconos/prove.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Proveedores</span>
                </a>
                
                
                <div class="subitems">
                    <a href="/proveedores">Ver todos</a>
                    <a href="/proveedores/create">Crear proveedor</a>
                    <a href="#" onclick="abrirPopup('importar-proveedores-popup');">Importar proveedores</a>
                </div>
            </li>
            <li class="nav-item small-nav {{ request()->is('productos/*') || request()->is('productos') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/productos">
                    <img src="{{asset('assets/images/iconos/produ.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Productos</span>
                </a>
                
                
                <div class="subitems">
                    <a href="/productos">Ver todos</a>
                    <a href="/productos/create">Crear producto</a>
                    <a href="#" onclick="abrirPopup('importar-productos-popup');">Importar productos</a>
                </div>
            </li>
          </ul>
      </div>
  </div>
  <!--=============== Left side End ================-->
