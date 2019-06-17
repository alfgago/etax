<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;

class UsersExport implements WithHeadings, WithMapping, FromQuery
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        
        $data = User::query()->with(['sales', 'companies']);
        
        return $data;
    }
    
    public function map($map): array
    {
        $plans = [];
        foreach ( $map->sales as $s ) {
      		$plans[] = $s->product->name;
        }
        
        if( empty($map->sales) ){
            foreach ( $map->subscriptions as $s ) {
          		$plans[] = $s->plan->plan_type . " " . $s->plan->plan_tier;
            }
        }
      	
      	$comps = [];
        foreach ( $map->companies as $c ) {
      		$comps[] = $c->name . " " . $c->business_name;
        }
        
        return [
            $map->first_name . " " . $map->last_name . " " . $map->last_name2,
            $map->email,
            implode(", ", $comps),
            implode(", ", $plans),
            $map->created_at,
        ];
    }								

     public function headings(): array 
     {
        return [
            'Nombre',
            'Correo',
            'Empresa',
            'Suscripción',
            'Fecha de registro'
        ];
    }


}
