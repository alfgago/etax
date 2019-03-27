<?php

namespace App\Imports;

use App\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
//use Maatwebsite\Excel\Concerns\ShouldQueue;

class ClientImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $company_id = auth()->user()->companies->first()->id;
        
        return new Client([
            'code' => $row['codigo'],
            'company_id' => 1,
            'tipo_persona' => $row['tipopersona'],
            'id_number' => $row['identificacion'],
            'first_name' => $row['nombre'],
            'last_name' => $row['primerapellido'],
            'last_name2' => $row['segundoapellido'],
            'email' => $row['correo'],
            'billing_emails' => $row['correoscopia'],
            'country' => $row['pais'],
            'state' => $row['provincia'],
            'city' => $row['canton'],
            'district' => $row['distrito'],
            'neighborhood' => $row['barrio'],
            'address' => $row['direccion'],
            'phone_area' => $row['areatel'],
            'phone' => $row['telefono'],
            'es_exento' => $row['exento'],
            'emisor_receptor' => $row['emisorreceptor'],
        ]);
    }
    
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
        return 1000;
    }    
    
}
