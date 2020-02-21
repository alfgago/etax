@extends('layouts/app')

@section('title')
    Perfil de empresa: {{ currentCompanyModel()->name }}
@endsection

@section('breadcrumb-buttons')

	<button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar configuración QuickBooks</button>

@endsection

@section('content')
<style>
.operative-data-tabs {
    display: flex;
}

.data-tab {
    margin-right: .25rem;
    padding: .25rem .5rem;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    cursor: pointer;
    transition: .5s ease all;
}

.data-tab:hover {
    background: #f5f5f5;
}

.data-tab.is-active {
    background: #e5e5e5;
}

.form-row.data-fields {
    margin: 0;
    border: 2px solid #e5e5e5;
    padding: .25rem;
}

.form-row.data-fields:not(.is-active) {
    display: none !important;
}

.menu-integraciones {
    display: inline-block;
    margin: 1rem 0;
    border-bottom: 0.45rem solid #F0C962;
}

.switch {
    font-size: 1.4rem !important;
    padding-left: 3.6em !important;
    padding-right: 0 !important;
}

.switch .slider {
    height: 1.3em;
    width: 3em;
}

.switch .slider:before {
    height: 1.5em;
    width: 1.5em;
    bottom: -.1em;
}

.switch input:checked + .slider:before {
    -webkit-transform: translateX(100%);
    transform: translateX(100%);
    margin-left: .15em;
}

</style>
<div class="row">
  <div class="col-md-12">
  	<div class="tabbable verticalForm">
    	<div class="row">
            <div class="col-sm-3">
                <ul class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                	<?php 
    				$menu = new App\Menu;
    				$items = $menu->menu('menu_empresas');
    				foreach ($items as $item) { ?>
    					<li>
    						<a class="nav-link" aria-selected="false"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
    					</li>
    				<?php } ?>
                </ul>
                <h3 class="menu-integraciones">Integraciones</h3>
                <ul class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                    <li>
                        <a  class="nav-link active" aria-selected="false" style="color: #ffffff;" href="/quickbooks/config" >QuickBooks</a>
                    </li>
                </ul>
            </div>
        <div class="col-sm-9">
          <div class="tab-content">       
						
			<form method="POST" action="/quickbooks/guardar-config/{{ currentCompanyModel()->id }}">
			
    			  @csrf
    			  
    			  <div class="form-row">
    			  	
    			    <div class="form-group col-md-12">
    			      <h3>
    			        Integración - QuickBooks
    			      </h3>
    			      <div class="integracion-description" style="font-size: 1rem; color: #999">
    			          Al activar la integración con QuickBooks, se sumará un recurrente mensual de <b style="font-size: 1.5em; color: #000;">$50/mes</b> a su suscripción actual a partir de su próxima fecha de corte.
    			      </div>
    			    </div>
    			    
    			    <div class="form-group col-md-12 pt-4 pb-4">
                        <label class="switch pr-5 switch-success mr-3"><span>¿Desea activar la integración con QuickBooks?</span>
                            <input type="checkbox" checked="checked"><span class="slider"></span>
                        </label>
    			    </div>
    			    
    			    <div class="form-group col-md-12">
    			      <label for="integration_id">¿Qué versión de QuickBooks utiliza?</label>
    			      <select class="form-control" name="integration_id" id="integration_id" required>
    			        <option value="2" selected>Online</option>
    			        <option value="3" >Escritorio</option>
    			      </select>
    			    </div>
    			    
    			    <div class="form-group col-md-12">
    			      <label for="api_key">Clave API QuickBooks</label>
    			      <input class="form-control" name="api_key" type="text" placeholder="Clave API">
    			      <div class="description">
    			          ¿No sabe cómo obtener su clave de API? <a href="#">Haga clic aquí</a>
    			      </div>
    			    </div>
    			    
    			    <div class="form-group col-md-6">
    			      <label for="invoice_from">¿Desea emitir sus facturas utilizando QuickBooks o eTax?</label>
    			      <select class="form-control" name="invoice_from" id="invoice_from" required>
    			        <option value="1" selected>eTax</option>
    			        <option value="2" >QuickBooks</option>
    			      </select>
    			    </div>
    			    
    			    <div class="form-group col-md-6">
    			      <label for="use_etax_currency">¿Desea que eTax se encargue de registrar el tipo de cambio en QuickBooks diariamente?</label>
    			      <select class="form-control" name="use_etax_currency" id="use_etax_currency" required>
    			        <option value="1" selected>Sí</option>
    			        <option value="0" >No</option>
    			      </select>
    			    </div>
    			    
    			    <div class="form-group col-md-6">
    			      <label for="sync_providers">¿Desea sincronizar proveedores?</label>
    			      <select class="form-control" name="sync_providers" id="sync_providers" required>
    			        <option value="1" selected>Sí</option>
    			        <option value="0" >No</option>
    			      </select>
    			    </div>
    			    
    			    <div class="form-group col-md-6">
    			      <label for="sync_clients">¿Desea sincronizar clientes?</label>
    			      <select class="form-control" name="sync_clients" id="sync_clients" required>
    			        <option value="1" selected>Sí</option>
    			        <option value="0" >No</option>
    			      </select>
    			    </div>
    			    
    			    <div class="form-group col-md-6">
    			      <label for="sync_products">¿Desea sincronizar productos/inventario?</label>
    			      <select class="form-control" name="sync_products" id="sync_products" required>
    			        <option value="1" selected>Sí</option>
    			        <option value="0" >No</option>
    			      </select>
    			    </div>
    			    
    			    <button id="btn-submit" type="submit" class="hidden btn btn-primary">Guardar información</button>          
    			    
    			  </div>
			  
			</form>

          </div>
        </div>
      </div>
    </div>
  </div>  
</div>       

@endsection

@section('footer-scripts')
	<style>
		.form-button {
		    display: block;
		    margin: 0;
		    padding: 0.25rem 0.5rem;
		    font-size: 0.9rem;
		    height: calc(1.9695rem + 2px);
		}
	</style>

@endsection
