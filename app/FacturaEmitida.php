<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\LineaFacturaRecibida;
use App\Cliente;
use Illuminate\Database\Eloquent\Model;

class FacturaEmitida extends Model
{
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
  
    //Relacion con el cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
  
    //Relación con facturas emitidas
    public function lineas()
    {
        return $this->hasMany(LineaFacturaEmitida::class);
    }
  
    //Devuelve la fecha de generación en formato Carbon
    public function fechaGenerada()
    {
        return Carbon::parse($this->fecha_generada);
    }
  
    //Devuelve la fecha de vencimiento en formato Carbon
    public function fechaVencimiento()
    {
        return Carbon::parse($this->fecha_vencimiento);
    }
  
    public function agregarLinea( $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado )
    {
      return LineaFacturaEmitida::create([
        'factura_emitida_id' => $this->id,
        'numero_linea' => $numero_linea,
        'codigo' => $codigo,
        'nombre' => $nombre,
        'tipo_producto' => $tipo_producto,
        'unidad_medicion' => $unidad_medicion,
        'cantidad' => $cantidad,
        'precio_unitario' => $precio_unitario,
        'subtotal' => $subtotal,
        'total' => $total,
        'porc_descuento' => $porc_descuento,
        'razon_descuento' => $razon_descuento,
        'tipo_iva' => $tipo_iva,
        'porc_iva' => $porc_iva,
        'esta_exonerado' => $esta_exonerado,
      ]);
    }
  
    public function agregarEditarLinea( $lid, $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado )
    {
      if( $lid ){
        $linea = LineaFacturaEmitida::find($lid);
        //Revisa que la linea exista y pertenece a la factura actual. Asegura que si el ID se cambia en frontend, no se actualice.
        if( $linea && $linea->factura_emitida_id == $this->id ) {
          $linea->numero_linea = $numero_linea;
          $linea->codigo = $codigo;
          $linea->nombre = $nombre;
          $linea->tipo_producto = $tipo_producto;
          $linea->unidad_medicion = $unidad_medicion;
          $linea->cantidad = $cantidad;
          $linea->precio_unitario = $precio_unitario;
          $linea->subtotal = $subtotal;
          $linea->total = $total;
          $linea->porc_descuento = $porc_descuento;
          $linea->razon_descuento = $razon_descuento;
          $linea->tipo_iva = $tipo_iva;
          $linea->porc_iva = $porc_iva;
          $linea->esta_exonerado = $esta_exonerado;
          $linea->save();
        }
      }else {
        $linea = $this->agregarLinea( $numero_linea, $codigo, $nombre, $tipo_producto, $unidad_medicion, $cantidad, $precio_unitario, $subtotal, $total, $porc_descuento, $razon_descuento, $tipo_iva, $porc_iva, $esta_exonerado );
      }
      return $linea;
  }

}
