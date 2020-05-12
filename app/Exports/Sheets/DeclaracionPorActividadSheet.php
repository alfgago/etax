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

class DeclaracionPorActividadSheet implements WithHeadings, FromArray, WithEvents, WithTitle
{
    
    public function __construct( $year, $data )
    {
        $this->year = $year;
        $this->data = $data;
    }
    
    public function title(): string
    {
        return 'Compras y ventas por actividad';
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $sh = $event->sheet->getDelegate();
                foreach(range('A','Z') as $columnID) {
                    $sh->getColumnDimension("$columnID")->setWidth('20');
                    $sh->getColumnDimension("A$columnID")->setWidth('20');
                    $sh->getColumnDimension("B$columnID")->setWidth('20');
                    $sh->getColumnDimension("C$columnID")->setWidth('20');
                }
                $sh->getColumnDimension("B")->setWidth('80');
                $cellRangeHeaders = 'A2:CE2';
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle($cellRangeHeaders)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF4472C4');
                      
                $cellRangeHeaders = 'A1:CE1';
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
                $dataActividades = $monthlyData['dataActividades'];
                $nombreMes = $monthlyData['nombreMes'];
                foreach($dataActividades as $dataActividad){
                    $values = [];
                    $values[] = $nombreMes;
                    $values[] = $dataActividad['codigo'] . " " . $dataActividad['titulo'];
                    $dataActividad = array_values($dataActividad);
                    
                    for($i = 2; $i <= 16; $i++){
                        $cat = $dataActividad[$i];
                        $values[] = $cat['totales'] != 0  ? $cat['totales'] : '0';;
                        $dataCats = $cat['cats'];
                        foreach($dataCats as $dataCat){
                            $sum = $dataCat['monto0'] + $dataCat['monto1'] + $dataCat['monto2'] + $dataCat['monto3'] + $dataCat['monto4'];
                            $values[] = $sum != 0  ? $sum : '0';
                        }
                    }
                    $valueMap[] = $values;
                }
            }catch(\Exception $e){}
        }
        return $valueMap;
    }					

     public function headings(): array 
     {
        $data = $this->data[0];
        $year = $this->year;
        $empresa = $data['empresa'];
        $headings = [];
        $headings[] = 'Mes';
        $headings[] = 'Actividad';
        $dataActividad = array_values($data['dataActividades'][0]);
        for($i = 2; $i <= 16; $i++){
            $k = $i-1;
            $cat = $dataActividad[$i];
            $headings[] = "$k - " . $cat['title'];
            $dataCats = $cat['cats'];
            $j = 1;
            foreach($dataCats as $dataCat){
                $headings[] = "$k.$j - " . $dataCat['name'];
                $values[] = $dataCat['monto0'] + $dataCat['monto1'] + $dataCat['monto2'] + $dataCat['monto3'] + $dataCat['monto4'];
                $j++;
            }
        }
        return [
            [ "Datos de declaraciones $year de la empresa $empresa. Exportados desde etaxcr.com" ],
            $headings
        ];
    }
    
    
}
