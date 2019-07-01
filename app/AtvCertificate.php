<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AtvCertificate extends Model
{
	use SoftDeletes;

    protected $fillable = [
        'company_id',
        'user',
        'password',
        'key_url',
        'pin',
        'generated_date',
        'due_date'
    ];
    
    
    public function certificateExists() {
        return Storage::exists($this->key_url);
    }
    
}
