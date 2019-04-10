<div class="sidebar-dashboard">
    <div class="card-title">{{ $titulo }}</div>
    <div class="row">
        <div class="col-md-6 dato-compras">
            <label>Total de ventas</label>
            <div class="dato">₡{{ number_format( $data->invoices_subtotal, 0 ) }}</div>
        </div>
        <div class="col-md-6 dato-ventas">
            <label>Total de compras</label>
            <div class="dato">₡{{ number_format( $data->bills_subtotal, 0 ) }}</div>
        </div>
        <div class="col-md-12 dato-iva emitido">
            <label><span>IVA de fact. emitidas</span></label>
            <div class="dato">₡{{ number_format( $data->total_invoice_iva, 0 ) }}</div>
        </div>
        <div class="col-md-12 dato-iva recibido">
            <label><span>IVA de fact. recibidas</span></label>
            <div class="dato">₡{{ number_format( $data->total_bill_iva, 0 ) }}</div>
        </div>
        <div class="col-md-12 dato-iva acreditable">
            <label><span>IVA acreditable</span></label>
            <div class="dato">₡{{ number_format( $data->iva_deducible_operativo, 0 ) }}</div>
        </div>
        <div class="col-md-12 dato-iva asumido">
            <label><span>IVA por ajustar</span></label>
            <div class="dato">₡{{ number_format( ( $data->iva_no_deducible - $data->iva_acreditable_identificacion_plena ), 0 ) }}</div>
        </div>
        
        @if( $data->iva_acreditable_identificacion_plena > 0)
        <div class="col-md-12 dato-iva gastado">
            <label><span style="">Gasto por IVA no acreditable</span></label>
            <div class="dato">₡{{ number_format( $data->iva_acreditable_identificacion_plena, 0 ) }}</div>
        </div>
        @endif
        
        @if( $data->balance_operativo > 0)
          <div class="col-md-12 dato-iva saldo">
            <label><span>IVA por pagar</span></label>
            <div class="dato">₡{{ number_format( $data->balance_operativo, 0 ) }}</div>
          </div>
        @endif
        
        @if( $data->balance_operativo < 0)
          <div class="col-md-12 dato-iva cobrar">
            <label><span>IVA por cobrar</span></label>
            <div class="dato">₡{{ number_format( abs($data->balance_operativo), 0 ) }}</div>
          </div>
        @endif
        
        
    </div>
 </div>  
 
 
 <style>
     
.sidebar-dashboard {
    color: #000;
    max-width: 380px;
}

.sidebar-dashboard label {
    margin: 0;
    font-weight: bold;
    dispay: block;
    font-size: 14px;

}

.sidebar-dashboard .dato-iva label {
    position: relative;
    display: inline-block;
    font-size: 14px;
    width: 50%;
    width: calc(50% + 10px);
}

.sidebar-dashboard .dato-iva .dato {
    display: inline-block;
    font-size: 17px;
    padding: .25rem .5rem;
}

.dato-ventas .dato,
.dato-compras .dato {
    color: #fff;
    background: #2852A6;
    padding: .25rem .5rem;
    font-size: 17px;
    margin-bottom: .5rem;
}

.dato-ventas .dato {
    background: #7C51A1;
}

.sidebar-dashboard .dato-iva label:before {
    position: absolute;
    content: '';
    top: 50%;
    left: 0;
    width: 100%;
    height: 2px;
    background: #ddd;
    margin-top: -1px
}

.sidebar-dashboard .dato-iva label span {
    position: relative;
    background: #fff;
    padding: .25rem .5rem;
    padding-left: 1rem;
    font-size: .85em;
}

.sidebar-dashboard .dato-iva label:after {
    position: absolute;
    content: '';
    top: 0%;
    left: 0;
    width: .5rem;
    height: 100%;
    background: #ddd;
    margin-top: -1px
}

.sidebar-dashboard .dato-iva.emitido label:after {
    background: #385189;
}


.sidebar-dashboard .dato-iva.recibido label:after {
    background: #7E9FD5;
}


.sidebar-dashboard .dato-iva.acreditable label:after {
    background: #7B539C;
}

.sidebar-dashboard .dato-iva.asumido label:after {
    background: #332E88;
}

.sidebar-dashboard .dato-iva.cobrar label:after,
.sidebar-dashboard .dato-iva.saldo label:after {
    background: #EFBF41;
}

.sidebar-dashboard .dato-iva.gastado label:after {
    background: #E14A95;
}

.row.comparacion-prorratas label {
    font-size: 14px;
    font-weight: bold;
    margin: 0;
}

.row.comparacion-prorratas {
    text-align: center;
    color: #000;
    font-size: 16px;
    max-width: 400px;
    margin: auto;
}

.row.comparacion-prorratas > div {
    padding: .0 .5rem;
    border: 2px solid #000;
}

.row.comparacion-prorratas > div.dif {
    border-bottom: 0;
}

.row.comparacion-prorratas > div.ope {
    border-right: 0;
}

.row.comparacion-prorratas span {
    display: block;
}
     
 </style>