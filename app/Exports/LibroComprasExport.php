<?php

namespace App\Exports;

use App\Bill;
use App\BillItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LibroComprasExport implements WithHeadings, WithMapping, FromQuery, WithEvents
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
        $billItems = BillItem::query()
        ->with(['bill', 'bill.provider', 'productCategory', 'ivaType'])
        ->where('year', $this->year)
        ->where('month', $this->month)
        ->whereHas('bill', function ($query) use ($current_company){
            $query->where('company_id', $current_company)
            ->where('is_void', false)
            ->where('is_authorized', true)
            ->where('is_code_validated', true)
            ->where('accept_status', 1)
            ->where('hide_from_taxes', false);
        });
        
        return $billItems;
    }
     
    public function map($map): array
    {
        $factor = $map->bill->document_type != '03' ? 1 : -1;
        return [
            $map->bill->documentTypeName(),
            $map->bill->generatedDate()->format('d/m/Y'),
            $map->bill->providerName(),
            $map->bill->activity_company_verification ?? ($map->bill->commercial_activity ?? 'No indica'),
            $map->bill->document_number,
            $map->item_number,
            isset($map->name) ? $map->name : 'No indica',
            isset($map->ivaType) ? $map->ivaType->name : 'No indica',
            isset($map->productCategory) ? $map->productCategory->id . " - " . $map->productCategory->name : 'No indica',
            $map->bill->currency,
            $map->bill->currency_rate ?? '',
            round( $map->subtotal * $factor, 2),
            $map->iva_percentage . '%',
            round( $map->iva_amount * $factor, 2),
            round( $map->total * $factor , 2),
        ];
    }						

     public function headings(): array 
     {
        return [
            [
                'Tipo Doc.',
                'Fecha',
                'Proveedor',
                'Actividad',
                'Consecutivo',
                '# Línea',
                'Producto',
                'Tipo IVA',
                'Cat. Declaración',
                'Moneda',
                'Tipo Cambio',
                'Subtotal',
                'Tarifa IVA',
                'Monto IVA',
                'Total'
            ]
        ];
    }
    
    
}
