<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\FacturaEmitida;
use App\Company;
use Illuminate\Http\Request;

class FacturaEmitidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facturasEmitidas = FacturaEmitida::all();
        return view('FacturaEmitida/index', [
          'facturas' => $facturasEmitidas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("FacturaEmitida/create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $facturaEmitida = new FacturaEmitida();
        $empresa = Company::first();
        $facturaEmitida->empresa_id = $empresa->id;

        //Datos generales y para Hacienda
        $facturaEmitida->tipo_documento = "01";
        $facturaEmitida->clave_factura = "50601021900310270242900100001010000000162174804809";
        $facturaEmitida->numero_referencia = $empresa->numero_referencia + 1;
        $numero_doc = ((int)$empresa->numero_documento) + 1;
        $facturaEmitida->numero_documento = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
        $facturaEmitida->correos_envio = $request->correos_envio;
        $facturaEmitida->condicion_venta = $request->condicion_venta;
        $facturaEmitida->medio_pago = $request->medio_pago;
        $facturaEmitida->orden_compra = $request->orden_compra;
        $facturaEmitida->referencia = $request->referencia;
        $facturaEmitida->estado_hacienda = "01";
        $facturaEmitida->estado_pago = "01";
        $facturaEmitida->comprobante_pago = "VOUCHER-123451234512345";
        $facturaEmitida->metodo_generacion = "M";
      
        //Datos de cliente
        $facturaEmitida->nombre_cliente = $request->nombre_cliente;
        $facturaEmitida->tipo_identificacion_cliente = $request->tipo_identificacion_cliente;
        $facturaEmitida->identificacion_cliente = $request->identificacion_cliente;
        $facturaEmitida->nombre_cliente = $request->nombre_cliente;
        $facturaEmitida->correos_envio = $request->correos_envio;
        
        //Datos de factura
        $facturaEmitida->notas = $request->notas;
        $facturaEmitida->subtotal = $request->subtotal;
        $facturaEmitida->moneda = $request->moneda;
        $facturaEmitida->tipo_cambio = $request->tipo_cambio;
        $facturaEmitida->total = $request->total;
        $facturaEmitida->monto_iva = $request->monto_iva;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->fecha_generada . ' ' . $request->hora);
        $facturaEmitida->fecha_generada = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->fecha_vencimiento );
        $facturaEmitida->fecha_vencimiento = $fechaV;
      
        $facturaEmitida->save();
      
        foreach($request->lineas as $linea){
          $numero_linea = $linea['numero'];
          $codigo = $linea['codigo'];
          $nombre = $linea['nombre'];
          $tipo_producto = $linea['tipo_producto'];
          $unidad_medicion = $linea['unidad_medicion'];
          $cantidad = $linea['cantidad'];
          $precio_unitario = $linea['precio_unitario'];
          $subtotal = $linea['subtotal'];
          $total = $linea['total'];
          $porc_descuento = '0';
          $razon_descuento = '';
          $tipo_iva = $linea['tipo_iva'];
          $porc_iva = $linea['porc_iva'];
          $esta_exonerado = false;
          
          $facturaEmitida->agregarLinea( $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado );
        }
      
        return redirect('/facturas-emitidas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FacturaEmitida  $facturaEmitida
     * @return \Illuminate\Http\Response
     */
    public function show(FacturaEmitida $facturaEmitida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FacturaEmitida  $facturaEmitida
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $facturaEmitida = FacturaEmitida::findOrFail($id);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $facturaEmitida->metodo_generacion != 'M' ){
          return redirect('/facturas-emitidas');
        }  
      
        return view('FacturaEmitida/edit', compact('facturaEmitida') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FacturaEmitida  $facturaEmitida
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $facturaEmitida = FacturaEmitida::findOrFail($id);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $facturaEmitida->metodo_generacion != 'M' ){
          return redirect('/facturas-emitidas');
        }
      
        $empresa = $facturaEmitida->empresa;
      
        //Datos generales y para Hacienda
        $facturaEmitida->numero_referencia = $empresa->numero_referencia + 1;
        $numero_doc = ((int)$empresa->numero_documento) + 1;
        $facturaEmitida->numero_documento = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
        $facturaEmitida->correos_envio = $request->correos_envio;
        $facturaEmitida->condicion_venta = $request->condicion_venta;
        $facturaEmitida->medio_pago = $request->medio_pago;
        $facturaEmitida->orden_compra = $request->orden_compra;
        $facturaEmitida->referencia = $request->referencia;
        $facturaEmitida->estado_hacienda = "01";
        $facturaEmitida->estado_pago = "01";
        $facturaEmitida->comprobante_pago = "VOUCHER-123451234512345";
        $facturaEmitida->metodo_generacion = "M";
      
        //Datos de cliente
        $facturaEmitida->nombre_cliente = $request->nombre_cliente;
        $facturaEmitida->tipo_identificacion_cliente = $request->tipo_identificacion_cliente;
        $facturaEmitida->identificacion_cliente = $request->identificacion_cliente;
        $facturaEmitida->nombre_cliente = $request->nombre_cliente;
        $facturaEmitida->correos_envio = $request->correos_envio;
        
        //Datos de factura
        $facturaEmitida->notas = $request->notas;
        $facturaEmitida->subtotal = $request->subtotal;
        $facturaEmitida->moneda = $request->moneda;
        $facturaEmitida->tipo_cambio = $request->tipo_cambio;
        $facturaEmitida->total = $request->total;
        $facturaEmitida->monto_iva = $request->monto_iva;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->fecha_generada . ' ' . $request->hora);
        $facturaEmitida->fecha_generada = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->fecha_vencimiento );
        $facturaEmitida->fecha_vencimiento = $fechaV;
      
        $facturaEmitida->save();
      
        //Recorre las lineas de factura y las guarda
        $lids = array();
        foreach($request->lineas as $linea) {
          
          $lid = $linea['id'] ? $linea['id'] : 0;
          $numero_linea = $linea['numero'];
          $codigo = $linea['codigo'];
          $nombre = $linea['nombre'];
          $tipo_producto = $linea['tipo_producto'];
          $unidad_medicion = $linea['unidad_medicion'];
          $cantidad = $linea['cantidad'];
          $precio_unitario = $linea['precio_unitario'];
          $subtotal = $linea['subtotal'];
          $total = $linea['total'];
          $porc_descuento = '0';
          $razon_descuento = '';
          $tipo_iva = $linea['tipo_iva'];
          $porc_iva = $linea['porc_iva'];
          $esta_exonerado = false;
          
          $linea_modificada = $facturaEmitida->agregarEditarLinea( $lid, $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado );
        
          array_push( $lids, $linea_modificada->id );
        }
      
        foreach ( $facturaEmitida->lineas as $linea ) {
          if( !in_array( $linea->id, $lids ) ) {
            $linea->delete();
          }
        }
      
        return redirect('/facturas-emitidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FacturaEmitida  $facturaEmitida
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $facturaEmitida = FacturaEmitida::find($id);
        foreach ( $facturaEmitida->lineas as $linea ) {
          $linea->delete();
        }
        $facturaEmitida->delete();
        return redirect('/facturas-emitidas');
    }
}
