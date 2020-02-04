<?php

namespace App\Exports;

use App\Invoice;
use App\InvoiceItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LibroVentasExportSM implements WithHeadings, WithMapping, FromQuery, WithEvents
{
    
    public function __construct(int $year, int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('I')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('J')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('K')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('L')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('M')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('N')->setAutoSize(true);
            },
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $current_company = currentCompany();
        $invoiceItems = InvoiceItem::query()
        ->with(['invoice', 'invoice.client', 'productCategory', 'ivaType'])
        ->where('year', $this->year)
        ->where('month', $this->month)
        ->whereHas('invoice', function ($query) use ($current_company){
            $query
            ->where('company_id', $current_company)
            ->where('is_void', false)
            ->where('is_authorized', true)
            ->where('is_code_validated', true)
            ->where('hide_from_taxes', false);
        });
        
        return $invoiceItems;
    }
    
    /**
    INTEGRACION SM SEGUROS
    **/
    
    public function map($map): array
    {
        $factor = $map->invoice->document_type != '03' ? 1 : -1;
        $tipoCambio = $map->invoice->currency_rate;
        if( $map->invoice->currency == 'CRC' ) {
            $tipoCambio = 1;
        }
        $array = [
            $map->invoice->documentTypeName(),
            $map->invoice->generatedDate()->format('d/m/Y'),
            $map->invoice->clientName(),
            $map->invoice->commercial_activity ?? 'No indica',
            $map->invoice->document_number,
            $map->item_number,
            isset($map->invoice->buy_order) ? $map->invoice->buy_order : "No indica",
            $map->code ?? 'N/A',
            $map->name,
            $map->ivaType ? $map->ivaType->name : 'No indica',
            isset($map->productCategory) ? ($map->productCategory->id . " - " . $map->productCategory->name) : 'No indica categoria',
            $map->invoice->currency,
            $map->invoice->currency_rate ?? '',
            $map->iva_percentage . '%',
            round( $map->subtotal * $factor, 2),
            round( $map->iva_amount * $factor, 2),
            round( $map->total * $factor , 2),
            round( $map->subtotal * $tipoCambio * $factor, 2),
            round( $map->iva_amount * $tipoCambio * $factor, 2),
            round( $map->total * $tipoCambio * $factor , 2),
            
        ];
        return $array;
    }						

     public function headings(): array 
     {
        $array = [
            [
                'Tipo Doc.',
                'Fecha',
                'Cliente',
                'Actividad',
                'Consecutivo',
                '# Línea',
                'Núm. Factura',
                'Cód. Referencia',
                'Producto',
                'Tipo IVA',
                'Cat. Declaración',
                'Moneda',
                'Tipo Cambio',
                'Tarifa IVA',
                'Subtotal',
                'Monto IVA',
                'Total',
                'Subtotal CRC',
                'Monto IVA CRC',
                'Total CRC'
            ]
        ];
        return $array;
    }
    
    /**
    END INTEGRACION SM SEGUROS
    **/
    
    
}
