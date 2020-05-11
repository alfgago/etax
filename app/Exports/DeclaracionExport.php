<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\DeclaracionPorActividadSheet;
use App\Exports\Sheets\DeclaracionImpuestosSheet;
use App\Exports\Sheets\DeclaracionDeterminacionSheet;

class DeclaracionExport implements WithMultipleSheets
{
    use Exportable;

    protected $year;
    protected $data;
    
    public function __construct(int $year, Array $data)
    {
        $this->year = $year;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new DeclaracionPorActividadSheet($this->year, $this->data);
        $sheets[] = new DeclaracionImpuestosSheet($this->year, $this->data);
        $sheets[] = new DeclaracionDeterminacionSheet($this->year, $this->data);
        
        return $sheets;
    }
}