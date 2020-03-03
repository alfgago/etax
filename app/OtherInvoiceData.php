<?php

namespace App;

use App\Invoice;
use App\InvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class OtherInvoiceData extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
  
    //Relacion con la factura
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
  
    //Relacion con la factura
    public function item()
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }
    
    public static function registerOtherData($invoiceId, $itemId, $code, $text, $showPdf = true, $showXml = false){
        
        $other = OtherInvoiceData::updateOrCreate(
            [
                'invoice_id' => $invoiceId,   
                'invoice_item_id' => $itemId,   
                'code' => $code 
            ],
            [
                'print_pdf' => $showPdf,    
                'print_xml' => $showXml,  
                'text' => $text
            ]
        );
        return $other;
    }
    
    
    
    public static function findData( $otherDataArray, $code, $itemId = null ){
        
        foreach($otherDataArray as $other){
            if( isset($itemId) ){
                if($other->code == $code && $itemId == $other->invoice_item_id){
                    return $other->text;
                }
            }else{
                if($other->code == $code){
                    return $other->text;
                }
            }
        }
        return null;
        
    }
}
