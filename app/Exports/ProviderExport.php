<?php

namespace App\Exports;

use App\Provider;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProviderExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Provider::all();
    }
}
