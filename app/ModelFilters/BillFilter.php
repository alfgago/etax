<?php namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Cache;

class BillFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function empresa($empresa)
    {
        $company = Cache::get("cache-api-company-".$empresa);
        return $this->where('company_id', $company->id);
    }

    public function mes($mes)
    {
        return $this->where('month', $mes);
    }

    public function ano($ano)
    {
        return $this->where('year', $ano);
    }

    public function proveedor($proveedor)
    {
        return $this->where('provider_id_number', $proveedor);
    }

    public function aceptado($aceptado)
    {
        return $this->where('accept_status', $aceptado);
    }

     public function validado($validado)
    {
        return $this->where('is_code_validated', $validado);
    }
    
}
