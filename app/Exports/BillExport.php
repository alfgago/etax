<?php

namespace App\Exports;

use App\Bill;
use App\BillItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BillExport implements FromCollection, WithHeadings, WithMapping
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $current_company = currentCompany();
        
        $billItems = BillItem::with(['bill', 'bill.provider'])->whereHas('bill', function ($query) use ($current_company){
            $query->where('company_id', $current_company);
        })->get();
        
        return $billItems;
    }
    
    public function map($map): array
    {
        return [
            $map->bill->document_type,
            $map->bill->document_number,
            $map->bill->currency,
            $map->bill->currency_rate,
            $map->bill->generatedDate()->format('d/m/Y'),
            $map->bill->dueDate()->format('d/m/Y') ,
            $map->bill->provider->code,
            $map->bill->provider->first_name . ' ' . $map->bill->provider->last_name . ' ' . $map->bill->provider->last_name2,
            $map->bill->provider->tipo_persona,
            $map->bill->provider->id_number,
            $map->bill->sale_condition,
            $map->bill->payment_type,
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
            $map->bill->total,
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
            'CodigoProveedor',
            'NombreProveedor',
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
