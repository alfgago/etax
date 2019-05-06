<?php

namespace App\Exports;

use App\Invoice;
use App\InvoiceItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

class InvoiceExport implements WithHeadings, WithMapping, FromQuery
{
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $current_company = currentCompany();
        
        $invoiceItems = InvoiceItem::query()->with(['invoice', 'invoice.client'])->whereHas('invoice', function ($query) use ($current_company){
            $query->where('company_id', $current_company);
        });
        
        return $invoiceItems;
    }
    
    public function map($map): array
    {
        return [
            $map->invoice->document_type,
            $map->invoice->document_number,
            $map->invoice->currency,
            $map->invoice->currency_rate,
            $map->invoice->generatedDate()->format('d/m/Y'),
            $map->invoice->dueDate()->format('d/m/Y') ,
            $map->invoice->client->code,
            $map->invoice->client->first_name . ' ' . $map->invoice->client->last_name . ' ' . $map->invoice->client->last_name2,
            $map->invoice->client->tipo_persona,
            $map->invoice->client->id_number,
            $map->invoice->sale_condition,
            $map->invoice->payment_type,
            $map->item_number,
            $map->name,
            $map->code,
            $map->item_count,
            $map->measure_unit,
            $map->unit_price,
            $map->subtotal,
            $map->discount,
            $map->iva_type,
            $map->iva_amount,
            $map->total,
            $map->invoice->total,
        ];
    }
    //  				
    // 								
    // 									

     public function headings(): array 
     {
        return [
            'IdTipoDocumento',
            'ConsecutivoComprobante',
            'IdMoneda',
            'TipoCambio',
            'FechaEmision',
            'FecgaVencimiento',
            'CodigoCliente',
            'NombreCliente',
            'TipoIdentificacion',
            'IdentificacionReceptor',
            'CondicionVenta',
            'MetodoPago',
            'NumeroLinea',
            'DetalleProducto',
            'CodigoProducto',
            'Cantidad',
            'UnidadMedicion',
            'PrecioUnitario',
            'SubtotalLinea',
            'MontoDescuento',
            'CodigoImpuesto',
            'MontoIVA',
            'TotalLinea',
            'TotalDocumento'
        ];
    }
    
    
}
