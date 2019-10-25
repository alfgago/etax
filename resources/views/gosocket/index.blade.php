@extends('layouts/gosocket') 

@section('title') GoSocket @endsection 

@section('content')
	<div class="row m-4">
		<div class="col-md-12">
			<h1 class="text-center titulo-principal-gs">¡Cumpla con el IVA! de forma ágil y sin preocupación!</h1>
			<p class="mt-4 mb-4 text-justify">
				Genere su declaración del Impuesto al Valor Agregado con base en su facturación en GoSocket. </br>
				Controle sus gastos, conozca su IVA por pagar mensual y sus derechos de acreditación de manera automática. 
			</p>
		</div>
	</div>
	<div class="row m-4">
		<div class="col-md-8">
			<h2 class="titulo-secundario-gs mt-4">Con eTax usted gana tranquilidad</h2>
				<p>
					eTax es un motor de cálculos automáticos para el IVA. Procesamos su información de ventas y compras para generar lo que necesita saber sobre el impuesto:</br>
					<ul>
						<li>Proyecciones de liquidación anual del IVA.</li>
						<li>Saldos a favor mensuales y acumulados.</li>
						<li>Información contable para la toma rápida de decisiones.</li>
					</ul>
			</p>
		</div>
		<div class="col-md-4 text-center">
			<img src="https://etaxcr.com/wp-content/uploads/2019/07/home.png" class="image-responsive" height="250px">
		</div>
	</div>
	<div class="row m-4">
		<div class="col-md-5 mt-4">
			<div class="text-center">
				<img src="https://etaxcr.com/wp-content/uploads/2019/07/planes-a-1.png" class="image-responsive mt-4" height="200px">
			</div>
			<h2 class="titulo-secundario-gs m-4" >Elija el plan de su conveniencia: </h2>
			<p>
				Complete el formulario y conozca cuál es el plan más adecuado para su negocio:  
			</p>
			<form>
			  <div class="form-group">
			    <label for="facturas-emitidas">Cantidad de facturas emitidas al mes que debemos procesar:</label>
			    <input type="range" class="form-control-range " id="facturas-emitidas" name="facturas-emitidas" value="25" min="1" max="10001" step="1">
			  </div>
			  <div class="form-group">
			    <label for="facturas-recibidas">Cantidad de facturas recibidas al mes que debemos procesar: </label>
			    <input type="range" class="form-control-range" id="facturas-recibidas" name="facturas-recibidas" value="25" min="10" max="10001" step="1">
			  </div>
			  <button type="submit" class="btn btn-primary">Enviar</button>
			</form>
		</div>
		<div class="col-md-7">
			
			<div class="slider">
			    <div class="item">
			    	<div class="plan-column" id="">
                        <div class="plan-header text-center n-h">
    						<span class="titulo">Profesional Básico</span>
						    <div class="precio">
						        <small>Desde</small>$9.99 <small>+ IVA / mes (pago anual)</small>
						    </div>
						    <div class="precio-mensual">$11.99 + sIVA / mes (pago mensual)</div>
						</div>
						<div class="plan-detail">
					        <div class="plan-feature title">Funcionalidades</div>
					        <div class="plan-feature si">
					            <i class="fa fa-check" aria-hidden="true"></i>Cálculo de IVA por pagar mensual    
					        </div>
						    <div class="plan-feature si">
						        <i class="fa fa-check" aria-hidden="true"></i>Borrador de declaración de IVA    
						    </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Derechos de acreditación (operativos y reales)
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Proyección de liquidación anual
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Reportes del IVA y del negocio    
					        </div>
					        <div class="plan-feature title">Procesamiento de</div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Facturación electrónica incluida    
					        </div>
					    </div>                                        
                        <div id="prueba" class="texto-promocion">
                            <p>Disfrute de sus 48 horas gratis!</p>
                        </div>
		                <div class="plan-button">
		                	<a href="https://app.etaxcr.com/register">Comprar ya</a>
		                </div>
		            </div>
			    </div>
			    <div class="item">
			    	<div class="plan-column" id="">
                        <div class="plan-header text-center n-h">
    						<span class="titulo">Profesional Intermedio</span>
						    <div class="precio">
						        <small>Desde</small>$9.99 <small>+ IVA / mes (pago anual)</small>
						    </div>
						    <div class="precio-mensual">$11.99 + sIVA / mes (pago mensual)</div>
						</div>
						<div class="plan-detail">
					        <div class="plan-feature title">Funcionalidades</div>
					        <div class="plan-feature si">
					            <i class="fa fa-check" aria-hidden="true"></i>Cálculo de IVA por pagar mensual    
					        </div>
						    <div class="plan-feature si">
						        <i class="fa fa-check" aria-hidden="true"></i>Borrador de declaración de IVA    
						    </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Derechos de acreditación (operativos y reales)
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Proyección de liquidación anual
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Reportes del IVA y del negocio    
					        </div>
					        <div class="plan-feature title">Procesamiento de</div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Facturación electrónica incluida    
					        </div>
					    </div>                                        
                        <div id="prueba" class="texto-promocion">
                            <p>Disfrute de sus 48 horas gratis!</p>
                        </div>
		                <div class="plan-button">
		                	<a href="https://app.etaxcr.com/register">Comprar ya</a>
		                </div>
		            </div>
			    </div>
			    <div class="item">
			    	<div class="plan-column" id="">
                        <div class="plan-header text-center n-h">
    						<span class="titulo">Profesional Pro</span>
						    <div class="precio">
						        <small>Desde</small>$9.99 <small>+ IVA / mes (pago anual)</small>
						    </div>
						    <div class="precio-mensual">$11.99 + sIVA / mes (pago mensual)</div>
						</div>
						<div class="plan-detail">
					        <div class="plan-feature title">Funcionalidades</div>
					        <div class="plan-feature si">
					            <i class="fa fa-check" aria-hidden="true"></i>Cálculo de IVA por pagar mensual    
					        </div>
						    <div class="plan-feature si">
						        <i class="fa fa-check" aria-hidden="true"></i>Borrador de declaración de IVA    
						    </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Derechos de acreditación (operativos y reales)
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Proyección de liquidación anual
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Reportes del IVA y del negocio    
					        </div>
					        <div class="plan-feature title">Procesamiento de</div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Facturación electrónica incluida    
					        </div>
					    </div>                                        
                        <div id="prueba" class="texto-promocion">
                            <p>Disfrute de sus 48 horas gratis!</p>
                        </div>
		                <div class="plan-button">
		                	<a href="https://app.etaxcr.com/register">Comprar ya</a>
		                </div>
		            </div>
			    </div>
			    <div class="item">
			    	<div class="plan-column" id="">
                        <div class="plan-header text-center n-h">
    						<span class="titulo">Empresarial Básico</span>
						    <div class="precio">
						        <small>Desde</small>$9.99 <small>+ IVA / mes (pago anual)</small>
						    </div>
						    <div class="precio-mensual">$11.99 + sIVA / mes (pago mensual)</div>
						</div>
						<div class="plan-detail">
					        <div class="plan-feature title">Funcionalidades</div>
					        <div class="plan-feature si">
					            <i class="fa fa-check" aria-hidden="true"></i>Cálculo de IVA por pagar mensual    
					        </div>
						    <div class="plan-feature si">
						        <i class="fa fa-check" aria-hidden="true"></i>Borrador de declaración de IVA    
						    </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Derechos de acreditación (operativos y reales)
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Proyección de liquidación anual
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Reportes del IVA y del negocio    
					        </div>
					        <div class="plan-feature title">Procesamiento de</div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Facturación electrónica incluida    
					        </div>
					    </div>                                        
                        <div id="prueba" class="texto-promocion">
                            <p>Disfrute de sus 48 horas gratis!</p>
                        </div>
		                <div class="plan-button">
		                	<a href="https://app.etaxcr.com/register">Comprar ya</a>
		                </div>
		            </div>
			    </div>
			    <div class="item">
			    	<div class="plan-column" id="">
                        <div class="plan-header text-center n-h">
    						<span class="titulo">Empresarial Intermedio</span>
						    <div class="precio">
						        <small>Desde</small>$9.99 <small>+ IVA / mes (pago anual)</small>
						    </div>
						    <div class="precio-mensual">$11.99 + sIVA / mes (pago mensual)</div>
						</div>
						<div class="plan-detail">
					        <div class="plan-feature title">Funcionalidades</div>
					        <div class="plan-feature si">
					            <i class="fa fa-check" aria-hidden="true"></i>Cálculo de IVA por pagar mensual    
					        </div>
						    <div class="plan-feature si">
						        <i class="fa fa-check" aria-hidden="true"></i>Borrador de declaración de IVA    
						    </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Derechos de acreditación (operativos y reales)
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Proyección de liquidación anual
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Reportes del IVA y del negocio    
					        </div>
					        <div class="plan-feature title">Procesamiento de</div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Facturación electrónica incluida    
					        </div>
					    </div>                                        
                        <div id="prueba" class="texto-promocion">
                            <p>Disfrute de sus 48 horas gratis!</p>
                        </div>
		                <div class="plan-button">
		                	<a href="https://app.etaxcr.com/register">Comprar ya</a>
		                </div>
		            </div>
			    </div>
			    <div class="item">
			    	<div class="plan-column" id="">
                        <div class="plan-header text-center n-h">
    						<span class="titulo">Empresarial Pro</span>
						    <div class="precio">
						        <small>Desde</small>$9.99 <small>+ IVA / mes (pago anual)</small>
						    </div>
						    <div class="precio-mensual">$11.99 + sIVA / mes (pago mensual)</div>
						</div>
						<div class="plan-detail">
					        <div class="plan-feature title">Funcionalidades</div>
					        <div class="plan-feature si">
					            <i class="fa fa-check" aria-hidden="true"></i>Cálculo de IVA por pagar mensual    
					        </div>
						    <div class="plan-feature si">
						        <i class="fa fa-check" aria-hidden="true"></i>Borrador de declaración de IVA    
						    </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Derechos de acreditación (operativos y reales)
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Proyección de liquidación anual
					        </div>
					        <div class="plan-feature si">
					                <i class="fa fa-check" aria-hidden="true"></i>Reportes del IVA y del negocio    
					        </div>
					        <div class="plan-feature title">Procesamiento de</div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>5</span> facturas emitidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Hasta <span>40</span> facturas recibidas    
					        </div>
					        <div class="plan-feature">
					        	<i class="fa fa-caret-right" aria-hidden="true"></i>Facturación electrónica incluida    
					        </div>
					    </div>                                        
                        <div id="prueba" class="texto-promocion">
                            <p>Disfrute de sus 48 horas gratis!</p>
                        </div>
		                <div class="plan-button">
		                	<a href="https://app.etaxcr.com/register">Comprar ya</a>
		                </div>
		            </div>
			    </div>
			</div>
		</div>
	</div>
	<div class="row m-4">
		<div class="col-md-8">
			<h2  class="titulo-secundario-gs">Respaldo</h2>
			<p>
				<ul>
					<li>eTax le da tranquilidad y le permite continuar con su trabajo sin mayor preocupación por los impuestos. Nuestra solución cuenta con una alta rigurosidad técnica para que todo sea acorde a lo que especifica el Ministerio de Hacienda.</li>
					<li>Nuestro aliado tributario cuenta con más de 30 años de experiencia y fiscaliza constantemente que nuestros cálculos y procedimientos sean los adecuados.</li>
					<li>Un ingreso al día y automatizado de los datos de ventas y compras le permite ahorrar tiempo y conocer el estado real de su negocio.</li>
				</ul>
			</p>
		</div>
		<div class="col-md-4">
			<img src="https://etaxcr.com/wp-content/uploads/2019/07/planes-a-1.png" class="image-responsive" height="250px">
		</div>
	</div>

@endsection