
  <div class="side-content-wrap">
      <div class="sidebar-left open" >
          <ul class="navigation-left">
            <li class="nav-item {{ request()->is('/') ? 'active' : '' }}" >
                <a class="nav-item-hold" href="/">
                    <img src="{{asset('assets/images/iconos/dashb.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Escritorio</span>
                </a>
                
            </li>
            
            
            @if( allowTo('invoicing') )
            <li class="nav-item {{ request()->is('facturas-emitidas/*') || request()->is('facturas-emitidas') ? 'active' : '' }}" id="ventas">
                <a class="nav-item-hold" href="/facturas-emitidas">
                    <img src="{{asset('assets/images/iconos/ventas.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Ventas</span>
                </a>

                <div class="subitems">
                    <a href="/facturas-emitidas">Ver todas</a>
                    <a href="/facturas-emitidas/emitir-factura/01">Emitir factura electrónica</a>
                    <a href="/facturas-emitidas/create" >Registrar factura existente</a>
                    <a href="#" onclick="abrirPopup('importar-emitidas-popup');">Importar facturas</a>
                    <a href="/facturas-emitidas/validaciones">Validar facturas</a>
                    <a href="/facturas-emitidas/autorizaciones">Autorizar facturas por email</a>
                </div>
            </li>
            @endif
            
            @if( allowTo('billing') )
            <li class="nav-item {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" id="compras">
                <a class="nav-item-hold" href="/facturas-recibidas">
                    <img src="{{asset('assets/images/iconos/compras.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Compras</span>
                </a>
                
                <div class="subitems">
                    <a href="/facturas-recibidas">Ver todas</a>
                    <a href="/facturas-recibidas/create">Registrar factura existente</a>
                    <a href="#" onclick="abrirPopup('importar-recibidas-popup');">Importar facturas</a>
                    <a href="/facturas-recibidas/aceptaciones">Aceptación de facturas recibidas</a>
                    <a href="/facturas-recibidas/autorizaciones">Autorizar facturas por email</a>
                </div>
            </li>
            @endif
            
            @if( allowTo('invoicing') || allowTo('billing') )
            <li class="nav-item small-nav {{ request()->is('facturas-recibidas/*') || request()->is('facturas-recibidas') ? 'active' : '' }}" id="facturacion">
                <a class="nav-item-hold" href="/facturas-emitidas">
                    <img src="{{asset('assets/images/iconos/facturacion.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Facturación</span>
                </a>
                
                <div class="subitems">
                    @if( allowTo('invoicing') )
                    <a href="/facturas-emitidas">Ver documentos emitidos</a>
                    <a href="/facturas-emitidas/emitir-factura/01">Emitir factura electrónica</a>
                    <a href="/facturas-emitidas/emitir-factura/09">Emitir factura electrónica de exportación</a>
                    <a href="/facturas-emitidas/emitir-factura/08">Emitir factura electrónica de compra</a>
                    <a href="/facturas-emitidas/emitir-factura/04">Emitir tiquete electrónico</a>
                    <a style="display:none; !important" href="/facturas-emitidas/emitir-factura/02">Emitir nota de débito</a>
                    @endif
                    @if( allowTo('invoicing') )
                    <a href="/facturas-recibidas/aceptaciones">Aceptación de facturas recibidas</a>
                    @endif
                </div>
            </li>
            @endif
            
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
            
            @if( allowTo('books') )
            <li class="nav-item small-nav {{ request()->is('reportes/*') || request()->is('reportes') ? 'active' : '' }}" id="cierresmes">
                <a class="nav-item-hold" href="/cierres">
                    <img src="{{asset('assets/images/iconos/report.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Cierres de mes</span>
                </a>
                
            </li>
            @endif
            
            @if( allowTo('reports') )
            <li class="nav-item small-nav {{ request()->is('reportes/*') || request()->is('reportes') ? 'active' : '' }}" id="reportes">
                <a class="nav-item-hold" href="/reportes">
                    <img src="{{asset('assets/images/iconos/report.png')}}" class="sidemenu-icon">
                    <span class="nav-text">Reportes</span>
                </a>
                
            </li>
            @endif
            
            @if( allowTo('catalogue') )
            <li class="nav-item small-nav {{ request()->is('clientes/*') || request()->is('clientes') ? 'active' : '' }}" id="clientes">
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
            <li class="nav-item small-nav {{ request()->is('proveedores/*') || request()->is('proveedores') ? 'active' : '' }}" id="proveedores">
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
            <li class="nav-item small-nav {{ request()->is('productos/*') || request()->is('productos') ? 'active' : '' }}" id="productos">
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
            @endif
            
          </ul>
      </div>
  </div>
  <script>
      /*$( "#factExistente" ).mouseover(function() {
          $( "#factExistente01" ).attr('hidden', false);
          $( "#factExistente02" ).attr('hidden', false);
          $( "#factExistente03" ).attr('hidden', false);
      });
      $( "#factExistente" ).mouseleave(function() {
          $( "#factExistente01" ).attr('hidden', true);
          $( "#factExistente02" ).attr('hidden', true);
          $( "#factExistente03" ).attr('hidden', true);
      });*/
  </script>
  <!--=============== Left side End ================-->
