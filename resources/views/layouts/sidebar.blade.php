  <div class="side-content-wrap">
      <div class="sidebar-left open" >
          <ul class="navigation-left">
            <?php 
            
                $menu = new App\Menu;
                $items = $menu->menu('menu_sidebar');
                $i = 0;
                foreach ($items as $item) {
                    ?>
                    <li class="nav-item <?php if($i > 2){ ?> small-nav <?php } ?>" >
                        <a class="nav-item-hold" {{$item->type}}="{{$item->link}}">
                            <img src="{{asset($item->icon)}}" class="sidemenu-icon">
                            <span class="nav-text">{{$item->name}}</span>
                        </a>
                        <div class="subitems">
                            <?php
                            $subitems = $item->subitems;
                            foreach ($subitems as $subitem) {
                                ?><a {{$subitem->type}}="{{$subitem->link}}">{{$subitem->name}}</a><?php
                            }
                            ?>  
                        </div>
                    </li> <?php 
                    $i++;
                }
                if(currentCompanyModel()->id==1110) {
                    ?>
                    <li class="nav-item small-nav" >
                        <a class="nav-item-hold" href="/sm">
                            <img src="/assets/images/iconos/facturacion.png" class="sidemenu-icon">
                            <span class="nav-text">Excel SM</span>
                        </a>
                        <div class="subitems"></div>
                    </li> 
                    <?php 
                }
                if(currentCompanyModel()->id_number==3101707070 ) {
                    ?>
                    <li class="nav-item small-nav" >
                        <a class="nav-item-hold" href="/quickbooks/mapeo-variables">
                            <img src="/assets/images/qb.png" class="sidemenu-icon">
                            <span class="nav-text">QuickBooks</span>
                        </a>
                         <div class="subitems" style="
                                bottom: 0;
                                top: auto;
                            ">
                            <a href="/quickbooks/mapeo-variables">Mapeo de variables</a>
                            <a href="/quickbooks/emitidas/comparativo">Sincronización de ventas</a>
                            <a href="/quickbooks/recibidas/comparativo">Sincronización de compras</a>
                            <a href="/quickbooks/clientes/comparativo">Sincronización de clientes</a>
                            <a href="/quickbooks/proveedores/comparativo">Sincronización de proveedores</a>
                            <a href="/quickbooks/productos/comparativo">Sincronización de productos</a>
                        </div>
                    </li> 
                    <?php 
                }
            ?>
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