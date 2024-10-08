<?php

namespace App\Imports;

use App\Client;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ClientImport implements WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    
    public function headingRow(): int 
    {
        return 3;
    }
    
    public function batchSize(): int
    {
        return 500;
    }
    
    public function chunkSize(): int
    {
        return 1000;
    }  
    
}

