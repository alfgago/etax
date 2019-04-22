<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    
       
    public static function clearTaxesCache($current_company, $month, $year){
      $cacheKey = "cache-taxes-$current_company-$month-$year";
      Cache::forget($cacheKey);
    }
    
    
    public static function clearCierreCache($current_company, $month, $year){
      $cacheKey = "cache-estadoCierre-$current_company-$month-$year";
      Cache::forget($cacheKey);
    }
    
    
}
