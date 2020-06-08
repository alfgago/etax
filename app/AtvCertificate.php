<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use \Carbon\Carbon;

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
    
    /**
     * Retorna la fecha de vencimiento del certificado ATV.
     **/
    public function getDueDate(){
        $cacheKey = "cache-atv-duedate-$this->id";
        if (!Cache::has($cacheKey)) {
            $certs = [];
            $file = Storage::get($this->key_url);
            openssl_pkcs12_read($file, $certs, $this->pin);
            $publicKey = $certs["cert"];
            $certData = openssl_x509_parse($publicKey);
            $expDate = Carbon::parse($certData['validTo_time_t']);
            Cache::put($cacheKey, $expDate, now()->addHours(48));
        }
        $expDate = Cache::get($cacheKey);
        return $expDate;
    }
    
    
    /**
     * Retorna si la fecha de vencimiento esta o no vencida
     **/
    public function isVencido(){
        try{
            $expDate = $this->getDueDate();
            $now = Carbon::now();
            $isVencido = $expDate < $now;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            $isVencido = false;
        }
        return $isVencido;
    }
    
}
