<?php

namespace App\Exports;

use App\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $current_company = currentCompany();
        $clients = Client::select( \DB::raw(
            "code, tipo_persona, id_number, first_name, last_name, last_name2, email, billing_emails, country, 
            state, city, district, neighborhood, address, phone_area, phone, es_exento, emisor_receptor"
        ))
        ->where('company_id', $current_company)
        ->get();
        
        return $clients;
    }
    
     public function headings(): array {
        return [
            'Codigo',
            'TipoPersona',
            'Identificacion',
            'PrimerNombre',
            'PrimerApellido',
            'SegundoApellido',
            'Correo',
            'CorreosCopia',
            'Pais',
            'Provincia',
            'Canton',
            'Distrito',
            'Barrio',
            'Direccion',
            'AreaTel',
            'Telefono',
            'Exento',
            'EmisorReceptor',
        ];
    }
    
    /*
    
    $table->unsignedBigInteger(company_id);
            $table->string(tipo_persona);
            $table->string(id_number);
            $table->string(code);
            $table->string(first_name);
            $table->string(last_name)->nullable();
            $table->string(last_name2)->nullable();
            $table->string(email)->unique();
            $table->string(emisor_receptor)->default(false);
            $table->string(country);
            $table->string(state)->nullable(); //Provincia
            $table->string(city)->nullable(); //Canton
            $table->string(district)->nullable(); //Distrito
            $table->string(neighborhood)->nullable(); //Barrio
            $table->string(zip)->nullable();
            $table->string(address)->nullable();
            $table->string(phone_area)->nullable();
            $table->string(phone)->nullable();
            $table->boolean(es_exento)->default(false);
            $table->string(billing_emails)->nullable();
    
    */
}
