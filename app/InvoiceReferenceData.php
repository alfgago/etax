<?php

namespace App;

use App\Invoice;
use App\InvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class InvoiceReferenceData extends Model
{
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
    
    public static function addReference($invoice, $data){
        
        $ref = InvoiceReferenceData::updateOrCreate(
            [
                'invoice_id' => $invoice->id,   
                'number' => $data['number'], 
            ],
            [
                'docType' => $data['docType'],    
                'code' => $data['code'],    
                'generated_date' => $invoice->generated_date,    
                'reason' => $data['reason']
            ]
        );
        return $ref;
    }
    
    /**
     *  $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            
            $table->string('docType')->nullable(); 
            $table->string('number')->nullable(); 
            $table->string('code')->nullable(); 
            $table->string('reason')->nullable(); 
            $table->timestamp('generated_date')->nullable();
     */
}
