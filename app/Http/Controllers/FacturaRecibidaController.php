<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\FacturaRecibida;
use App\Company;
use Illuminate\Http\Request;

class FacturaRecibidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facturasRecibidas = FacturaRecibida::all();
        return view('FacturaRecibida/index', [
          'facturas' => $facturasRecibidas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("FacturaRecibida/create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        $facturaRecibida = new FacturaRecibida();
        $empresa = Company::first();
        $facturaRecibida->empresa_id = $empresa->id;
      
        //Datos generales y para Hacienda
        $facturaRecibida->tipo_documento = "01";
        $facturaRecibida->clave_factura = "50601021900310270242900100001010000000162174804809";
        $facturaRecibida->numero_referencia = $empresa->numero_referencia + 1;
        $numero_doc = ((int)$empresa->numero_documento) + 1;
        $facturaRecibida->numero_documento = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
        //$facturaRecibida->correos_envio = $request->correos_envio;
        $facturaRecibida->condicion_venta = $request->condicion_venta;
        $facturaRecibida->medio_pago = $request->medio_pago;
        $facturaRecibida->orden_compra = $request->orden_compra;
        $facturaRecibida->referencia = $request->referencia;
        $facturaRecibida->estado_hacienda = "01";
        $facturaRecibida->estado_pago = "01";
        $facturaRecibida->comprobante_pago = "VOUCHER-123451234512345";
        $facturaRecibida->metodo_generacion = "M";
      
        //Datos de proveedor
        $facturaRecibida->proveedor = $request->proveedor;
        $facturaRecibida->servicio = "";
        
        //Datos de factura
        $facturaRecibida->notas = $request->notas;
        $facturaRecibida->subtotal = $request->subtotal;
        $facturaRecibida->moneda = $request->moneda;
        $facturaRecibida->tipo_cambio = $request->tipo_cambio;
        $facturaRecibida->total = $request->total;
        $facturaRecibida->monto_iva = $request->monto_iva;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->fecha_recibida . ' ' . $request->hora);
        $facturaRecibida->fecha_recibida = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->fecha_vencimiento );
        $facturaRecibida->fecha_vencimiento = $fechaV;
      
        $facturaRecibida->save();

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
          
          $facturaRecibida->agregarLinea( $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado );
        }
      
        return redirect('/facturas-recibidas');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FacturaRecibida  $facturaRecibida
     * @return \Illuminate\Http\Response
     */
    public function show(FacturaRecibida $facturaRecibida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FacturaRecibida  $facturaRecibida
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $facturaRecibida = FacturaRecibida::findOrFail($id);
        return view('FacturaRecibida/edit', compact('facturaRecibida') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FacturaRecibida  $facturaRecibida
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      
        $facturaRecibida = FacturaRecibida::findOrFail($id);
      
        //Valida que la factura emitida sea generada manualmente. De ser generada por XML o con el sistema, no permite edición.
        if( $facturaRecibida->metodo_generacion != 'M' ){
          return redirect('/facturas-recibidas');
        }
      
        $empresa = $facturaRecibida->empresa;
      
        //Datos generales y para Hacienda
        $facturaRecibida->tipo_documento = "01";
        $facturaRecibida->clave_factura = "50601021900310270242900100001010000000162174804809";
        $facturaRecibida->numero_referencia = $empresa->numero_referencia + 1;
        $numero_doc = ((int)$empresa->numero_documento) + 1;
        $facturaRecibida->numero_documento = str_pad($numero_doc, 20, '0', STR_PAD_LEFT);
        //$facturaRecibida->correos_envio = $request->correos_envio;
        $facturaRecibida->condicion_venta = $request->condicion_venta;
        $facturaRecibida->medio_pago = $request->medio_pago;
        $facturaRecibida->orden_compra = $request->orden_compra;
        $facturaRecibida->referencia = $request->referencia;
        $facturaRecibida->estado_hacienda = "01";
        $facturaRecibida->estado_pago = "01";
        $facturaRecibida->comprobante_pago = "VOUCHER-123451234512345";
        $facturaRecibida->metodo_generacion = "M";
      
        //Datos de proveedor
        $facturaRecibida->proveedor = $request->proveedor;
        $facturaRecibida->servicio = "";
        
        //Datos de factura
        $facturaRecibida->notas = $request->notas;
        $facturaRecibida->subtotal = $request->subtotal;
        $facturaRecibida->moneda = $request->moneda;
        $facturaRecibida->tipo_cambio = $request->tipo_cambio;
        $facturaRecibida->total = $request->total;
        $facturaRecibida->monto_iva = $request->monto_iva;

        //Fechas
        $fecha = Carbon::createFromFormat('d/m/Y g:i A', $request->fecha_recibida . ' ' . $request->hora);
        $facturaRecibida->fecha_recibida = $fecha;
        $fechaV = Carbon::createFromFormat('d/m/Y', $request->fecha_vencimiento );
        $facturaRecibida->fecha_vencimiento = $fechaV;
      
        $facturaRecibida->save();
      
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
          
          $linea_modificada = $facturaRecibida->agregarEditarLinea( $lid, $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado );
        
          array_push( $lids, $linea_modificada->id );
        }
      
        foreach ( $facturaRecibida->lineas as $linea ) {
          if( !in_array( $linea->id, $lids ) ) {
            $linea->delete();
          }
        }
      
        return redirect('/facturas-recibidas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FacturaRecibida  $facturaRecibida
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $facturaRecibida = FacturaRecibida::find($id);
        foreach ( $facturaRecibida->lineas as $linea ) {
          $linea->delete();
        }
        $facturaRecibida->delete();
        return redirect('/facturas-recibidas');
    }
}
