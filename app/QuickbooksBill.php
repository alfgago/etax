<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use \Carbon\Carbon;
use App\Bill;
use App\Quickbooks;
use App\Company;

class QuickbooksBill extends Model
{

    protected $guarded = [];
    use Sortable, SoftDeletes;

    protected $casts = [
        'qb_data' => 'array'
    ];
	
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }  

    public function bill(){
        return $this->belongsTo(Bill::class);
    }
    
}
