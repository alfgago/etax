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
use Rap2hpoutre\FastExcel\FastExcel;

class LibroVentasExport implements WithHeadings, WithMapping, FromQuery, WithEvents
{
    
    public function __construct(int $year, int $month, $companyId = null, $offset = 0, $limit = 35000)
    {
        $this->year = $year;
        $this->month = $month;
        $this->company_id = $companyId;
        $this->offset = $offset;
        $this->limit = $limit;
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
                $event->sheet->getDelegate()->getColumnDimension('O')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('P')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('R')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('S')->setAutoSize(true);
                $cellRangeHeaders = 'A1:S1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF4472C4');
            },
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $companyId = $this->company_id;
        if( !isset($companyId) ){
            $companyId = currentCompany();
        }
        $invoiceItems = InvoiceItem::query()
        ->with(['invoice', 'invoice.client', 'productCategory', 'ivaType'])
        ->where('year', $this->year)
        ->where('month', $this->month)
        ->where('company_id', $companyId)
        ->whereHas('invoice', function ($query) {
            $query
            ->where('is_void', false)
            ->where('is_authorized', true)
            ->where('is_code_validated', true)
            ->where('hide_from_taxes', false);
        });
        
        return $invoiceItems;
    }
     
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
            $map->code ?? 'N/A',
            $map->name,
            $map->ivaType ? $map->ivaType->name : 'No indica',
            isset($map->productCategory) ? ($map->productCategory->id . " - " . $map->productCategory->name) : 'No indica categoria',
            $map->invoice->currency,
            $map->invoice->currency_rate ?? '',
            (isset($map->ivaType) ? $map->ivaType->percentage : $map->iva_percentage) . '%',
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
    
    //Hace lo mismo que el map, solo que en este caso lo hace ya con el nombre de la columna
    public function mapLight($map): array
    {
        $factor = $map->invoice->document_type != '03' ? 1 : -1;
        $tipoCambio = $map->invoice->currency_rate;
        if( $map->invoice->currency == 'CRC' ) {
            $tipoCambio = 1;
        }
        $array = [
            "Tipo Doc." => $map->invoice->documentTypeName(),
            "Fecha" => $map->invoice->generatedDate()->format('d/m/Y'),
            "Cliente" => $map->invoice->clientName(),
            "Actividad" => $map->invoice->commercial_activity ?? 'No indica',
            "Consecutivo" => $map->invoice->document_number,
            "# Línea" => $map->item_number,
            "Cód. Referencia" => $map->code ?? 'N/A',
            "Producto" => $map->name,
            "Tipo IVA" => $map->ivaType ? $map->ivaType->name : 'No indica',
            "Cat. Declaración" => isset($map->productCategory) ? ($map->productCategory->id . " - " . $map->productCategory->name) : 'No indica categoria',
            "Moneda" => $map->invoice->currency,
            "Tipo Cambio" => $map->invoice->currency_rate ?? '',
            "Tarifa IVA" => (isset($map->ivaType) ? $map->ivaType->percentage : $map->iva_percentage) . '%',
            "Subtotal" => round( $map->subtotal * $factor, 2),
            "Monto IVA" => round( $map->iva_amount * $factor, 2),
            "Total" => round( $map->total * $factor , 2),
            "Subtotal CRC" => round( $map->subtotal * $tipoCambio * $factor, 2),
            "Monto IVA CRC" => round( $map->iva_amount * $tipoCambio * $factor, 2),
            "Total CRC" => round( $map->total * $tipoCambio * $factor , 2),
            
        ];
        return $array;
    }	
    
    //Descarga Excel usando un método mas liviano
    public function getLightExcel(){
        $items = $this->query()->get();
        $res = [];
        foreach($items as $item){
            $res[] = $this->mapLight($item);
        }
        $res = collect($res);
        
        //Genera el archivo Excel usando FastExcel
        $filename= "libro-ventas-$this->month-$this->year.xlsx";
        $file = (new FastExcel( $res ))->download($filename);
        return $file;
    }
}
