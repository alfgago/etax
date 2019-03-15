<?php

namespace App;

use \Carbon\Carbon;
use App\Company;
use App\InvoiceItem;
use App\Client;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
  
    //Relacion con el cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
  
    //Relación con facturas emitidas
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
  
    //Devuelve la fecha de generación en formato Carbon
    public function generatedDate()
    {
        return Carbon::parse($this->generated_date);
    }
  
    //Devuelve la fecha de vencimiento en formato Carbon
    public function dueDate()
    {
        return Carbon::parse($this->due_date);
    }
  
    public function addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $is_exempt )
    {
      return InvoiceItem::create([
        'invoice_id' => $this->id,
        'item_number' => $item_number,
        'code' => $code,
        'name' => $name,
        'product_type' => $product_type,
        'measure_unit' => $measure_unit,
        'item_count' => $item_count,
        'unit_price' => $unit_price,
        'subtotal' => $subtotal,
        'total' => $total,
        'discount_percentage' => $discount_percentage,
        'discount_reason' => $discount_reason,
        'iva_type' => $iva_type,
        'iva_percentage' => $iva_percentage,
        'iva_amount' => $iva_amount,
        'is_exempt' => $is_exempt,
      ]);
      
    }
  
    public function addEditItem( $item_id, $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $iva_amount, $is_exempt )
    {
      if( $item_id ){
        $item = InvoiceItem::find($item_id);
        //Revisa que la linea exista y pertenece a la factura actual. Asegura que si el ID se cambia en frontend, no se actualice.
        if( $item && $item->invoice_id == $this->id ) {
          $item->item_number = $item_number;
          $item->code = $code;
          $item->name = $name;
          $item->product_type = $product_type;
          $item->measure_unit = $measure_unit;
          $item->item_count = $item_count;
          $item->unit_price = $unit_price;
          $item->subtotal = $subtotal;
          $item->total = $total;
          $item->discount_percentage = $discount_percentage;
          $item->discount_reason = $discount_reason;
          $item->iva_type = $iva_type;
          $item->iva_percentage = $iva_percentage;
          $item->iva_amount = $iva_amount;
          $item->is_exempt = $is_exempt;
          $item->save();
        }
      }else {
        $item = $this->addItem( $item_number, $code, $name, $product_type, $measure_unit, $item_count, $unit_price, $subtotal, $total, $discount_percentage, $discount_reason, $iva_type, $iva_percentage, $is_exempt );
      }
      return $item;
    }

  
}
