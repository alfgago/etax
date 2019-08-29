<?php

namespace App\Imports;

use App\Invoice;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class InvoiceImportSM implements WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    
    public function headingRow(): int 
    {
        return 1;
    }
    
    public function batchSize(): int
    {
        return 500;
    }
    
    public function chunkSize(): int
    {
        return 500;
    }  
    
}
