<?php

namespace App\Exports\Sheets;

use App\Invoice;
use App\InvoiceItem;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DeclaracionImpuestosSheet implements WithHeadings, FromArray, WithEvents, WithTitle 
{
    
    public function __construct( $year, $data )
    {
        $this->year = $year;
        $this->data = $data;
    }
    
    public function title(): string
    {
        return 'Créditos y débitos fiscales';
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $sh = $event->sheet->getDelegate();
                foreach(range('A','Z') as $columnID) {
                    $sh->getColumnDimension("$columnID")->setWidth('35');
                    $sh->getColumnDimension("A$columnID")->setWidth('35');
                    $sh->getColumnDimension("B$columnID")->setWidth('35');
                }
                $cellRangeHeaders = 'A2:BN2';
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF4472C4');
                      
                $cellRangeHeaders = 'A1:BN1';
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF4472C4');
            },
        ];
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
        $data = $this->data;
        $year = $this->year;
        $valueMap = [];
        
        foreach($data as $monthlyData){
            $values = [];
            $dataImpuestos = $monthlyData['impuestos'];
            $nombreMes = $monthlyData['nombreMes'];
            $values[] = $nombreMes;
            foreach($dataImpuestos as $imp){
                $values[] = $imp['val'] != 0  ? $imp['val'] : '0';
            }
            $valueMap[] = $values;
        }
        return $valueMap;
    }					

     public function headings(): array 
     {
        $data = $this->data[9];
        $year = $this->year;
        $empresa = $data['empresa'];
        $headings = [];
        $headings[] = 'Mes';
        $dataImpuestos = $data['impuestos'];
        foreach($dataImpuestos as $imp){
            $headings[] = $imp['name'];
        }
        return [
            [ "Datos de declaraciones $year de la empresa $empresa. Exportados desde etaxcr.com" ],
            $headings
        ];
    }
    
    
}
