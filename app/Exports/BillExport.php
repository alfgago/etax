<?php

namespace App\Exports;

use App\Bill;
use Maatwebsite\Excel\Concerns\FromCollection;

class BillExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Bill::all();
    }
}
