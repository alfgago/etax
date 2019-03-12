<div class="card fullh">
  <div class="card-body text-left">
      <div class="card-title">{{ $titulo }}</div>
      <p class="text-20 text-muted m-0">Cuentas de crédito</p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Adquisiciones de bienes y servicios: <strong>  ₡{{ $data->cc_adquisiciones }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Anticipos a proveedores: <strong>  ₡{{ $data->cc_anticipos }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Adquisiciones de bienes de capital: <strong>  ₡{{ $data->cc_adq_capital }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPCFE de bienes y servicios: <strong>  ₡{{ $data->cc_hpcfe_adquisiciones }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPCFE de no deducibles: <strong>  ₡{{ $data->cc_hpcfe_adquisiciones_no_deducibles }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPCFE de pagos anticipados: <strong>  ₡{{ $data->cc_hpcfe_anticipos }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPCFE de bienes de capital: <strong>  ₡{{ $data->cc_hpcfe_adq_capital }} </strong></p>
    
      <p class="text-20 text-muted m-0">Cuentas de débito</p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Ventas al 1%: <strong>  ₡{{ $data->cc_ventas_1 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Ventas al 2%: <strong>  ₡{{ $data->cc_ventas_2 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Ventas al 13%: <strong>  ₡{{ $data->cc_ventas_13 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Ventas al 4%: <strong>  ₡{{ $data->cc_ventas_4 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Ventas al 13% con límite: <strong>  ₡{{ $data->cc_ventas_13_limite }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Anticipos al 1%: <strong>  ₡{{ $data->cc_anticipos_debito_1 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Anticipos al 2%: <strong>  ₡{{ $data->cc_anticipos_debito_2 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Anticipos al 3%: <strong>  ₡{{ $data->cc_anticipos_debito_3 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Anticipos al 4%: <strong>  ₡{{ $data->cc_anticipos_debito_4 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPDFE al 1%: <strong>  ₡{{ $data->cc_hpdfe_1 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPDFE al 2%: <strong>  ₡{{ $data->cc_hpdfe_2 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPDFE al 13%: <strong>  ₡{{ $data->cc_hpdfe_3 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">HPDFE al 4%: <strong>  ₡{{ $data->cc_hpdfe_4 }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Exportaciones y asimilados: <strong>  ₡{{ $data->cc_exportaciones }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Operaciones de estado y ONGs: <strong>  ₡{{ $data->cc_estado_ong }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Exenciones objetivas sin límite: <strong>  ₡{{ $data->cc_exenciones_objetivas_sin_limite }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Exenciones objetivas con límite: <strong>  ₡{{ $data->cc_exenciones_objetivas_con_limite }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Exenciones de autoconsumo: <strong>  ₡{{ $data->cc_exenciones_autoconsumo }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Exenciones subjetivas: <strong>  ₡{{ $data->cc_exenciones_subjetivas }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Exenciones a no sujetos: <strong>  ₡{{ $data->cc_exenciones_no_sujetos }} </strong></p>
    
      <p class="text-20 text-muted m-0">Saldo</p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Sumatoria de bases de crédito: <strong>  ₡{{ $data->cc_bases_credito }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Sumatoria de impuestos de crédito: <strong>  ₡{{ $data->cc_ivas_credito }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Sumatoria de bases de débito: <strong>  ₡{{ $data->cc_bases_debito }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Sumatoria de impuestos de débito: <strong>  ₡{{ $data->cc_ivas_debito }} </strong></p>
      <p class="text-12 text-muted m-0 p-2 border-bottom">Ajuste de saldo: <strong>  ₡{{ $data->cc_ajuste_saldo }} </strong></p>
  </div>
</div>