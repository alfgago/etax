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

class DeclaracionDeterminacionSheet implements WithHeadings, FromArray, WithEvents, WithTitle 
{
    
    public function __construct( $year, $data )
    {
        $this->year = $year;
        $this->data = $data;
    }
    
    public function title(): string
    {
        return 'Determinación y liquidación';
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $sh = $event->sheet->getDelegate();
                foreach(range('A','L') as $columnID) {
                    $sh->getColumnDimension("$columnID")->setWidth('35');
                }
                $cellRangeHeaders = 'A2:L2';
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF4472C4');
                      
                $cellRangeHeaders = 'A1:L1';
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
            try{
                $values = [];
                $dataDeterminacion = $monthlyData['determinacion'];
                $nombreMes = $monthlyData['nombreMes'];
                $values[] = $nombreMes;
                $values[] = $dataDeterminacion['impuestoOperacionesGravadas'] != 0 ? $dataDeterminacion['impuestoOperacionesGravadas'] : '0';
                $values[] = $dataDeterminacion['totalCreditosPeriodo'] != 0 ? $dataDeterminacion['totalCreditosPeriodo'] : '0';
                $values[] = $dataDeterminacion['devolucionIva'] != 0 ? $dataDeterminacion['devolucionIva'] : '0';
                $values[] = $dataDeterminacion['saldoFavorPeriodo'] != 0 ? $dataDeterminacion['saldoFavorPeriodo'] : '0';
                $values[] = $dataDeterminacion['saldoDeudorPeriodo'] != 0 ? $dataDeterminacion['saldoDeudorPeriodo'] : '0';
                $values[] = $dataDeterminacion['saldoFavorProrrataReal'] != 0 ? $dataDeterminacion['saldoFavorProrrataReal'] : '0';
                $values[] = $dataDeterminacion['saldoDeudorProrrataReal'] != 0 ? $dataDeterminacion['saldoDeudorProrrataReal'] : '0';
                $values[] = $dataDeterminacion['saldoFavorFinalPeriodo'] != 0 ? $dataDeterminacion['saldoFavorFinalPeriodo'] : '0';
                $values[] = $dataDeterminacion['impuestoFinalPeriodo'] != 0 ? $dataDeterminacion['impuestoFinalPeriodo'] : '0';
                $values[] = $dataDeterminacion['retencionImpuestos'] != 0 ? $dataDeterminacion['retencionImpuestos'] : '0';
                $values[] = $dataDeterminacion['saldoFavorAnterior'] != 0 ? $dataDeterminacion['saldoFavorAnterior'] : '0';
                
                $valueMap[] = $values;
            }catch(\Exception $e){}
        }
        return $valueMap;
    }					

     public function headings(): array 
     {
        $data = $this->data[0];
        $year = $this->year;
        $empresa = $data['empresa'];
        
        $headings[] = "Mes"; 
        $headings[] = "Impuesto generado por operaciones gravadas"; 
        $headings[] = "Total de créditos del periodo"; 
        $headings[] = "Devolución del IVA por servicios de salud privada pagados con tarjeta de crédito y/o débito"; 
        $headings[] = "Saldo a favor del periodo"; 
        $headings[] = "Impuesto neto del periodo (saldo deudor)"; 
        $headings[] = "Saldo a favor en aplicación del porcentaje de la liquidación final"; 
        $headings[] = "Saldo deudor en aplicación del porcentaje de la liquidación final"; 
        $headings[] = "Saldo a favor final"; 
        $headings[] = "Impuesto final"; 
        $headings[] = "Retenciones pagos a cuenta del impuesto"; 
        $headings[] = "Saldo a favor de periodos anteriores"; 
         
        return [
            [ "Datos de declaraciones $year de la empresa $empresa. Exportados desde etaxcr.com" ],
            $headings
        ];
    }
    
    
}
