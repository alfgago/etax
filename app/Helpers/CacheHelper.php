<?php

if (!function_exists('clearInvoiceCache')) {

    function clearInvoiceCache($invoice){
        $month = $invoice->month;
        $year = $invoice->year;
        clearTaxesCache($invoice->company_id, $month, $year);
        clearTaxesCache($invoice->company_id, 0, $year);
    }
    
}

if (!function_exists('clearBillCache')) {
	
    function clearBillCache($bill){
        $month = $bill->month;
        $year = $bill->year;
        clearTaxesCache($bill->company_id, $month, $year);
        clearTaxesCache($bill->company_id, 0, $year);
        $cacheKeyBillAccepts = "cache-billaccepts-".$this->id;
        Cache::forget($cacheKeyBillAccepts);
    }
    
}

if (!function_exists('clearTaxesCache')) {  
	
    function clearTaxesCache($current_company, $month, $year){
      	try{
          	$cacheKey = "cache-taxes-$current_company-$month-$year";
          	Cache::forget($cacheKey);
            if( auth()->user() ){
                $userId = auth()->user()->id;
                Cache::forget("cache-currentcompany-$userId");
            }
            
            //Fuerza el recalc de Calculated Taxes, pero solamente puede hacerse cada 15 segundos
            $taxkey = "calc-$cacheKey"; 
            if ( !Cache::has($taxkey) ) {
                $taxes = \App\CalculatedTax::where('company_id', $current_company)
                                            ->where('month', $month)
                                            ->where('year', $year)
                                            ->where('is_final', true)
                                            ->where('calculated', true)
                                            ->first();
                if( isset($taxes) ){
                    $taxes->calculated = false;
                    $taxes->save();
                    \Log::info("Limpio cache: $current_company : $year/$month");
                }
                Cache::put($taxkey, $taxes, 15);
            }
            
      	}catch(\Throwable $e){
      	    \Log::error("Fallo al limpiar cache: " . $e);
      	}
    }
    
}
    
if (!function_exists('clearCierreCache')) {    
	
    function clearCierreCache($current_company, $month, $year){
      $cacheKey = "cache-estadoCierre-$current_company-$month-$year";
      Cache::forget($cacheKey);
      $cacheKey2 = "cache-taxes-$current_company-$month-$year";
      Cache::forget($cacheKey2);
    }
    
}

if (!function_exists('clearLastTaxesCache')) {  
	
    function clearLastTaxesCache($current_company, $anoAnterior) {
      	Cache::forget("cache-lasttaxes-$current_company-0-$anoAnterior");
      	Cache::forget("cache-prorrata-$current_company-$anoAnterior");
      	
      	$year = $anoAnterior+1;
      	
        clearTaxesCache($current_company, 1, $year);
        clearTaxesCache($current_company, 2, $year);
        clearTaxesCache($current_company, 3, $year);
        clearTaxesCache($current_company, 4, $year);
        clearTaxesCache($current_company, 5, $year);
        clearTaxesCache($current_company, 6, $year);
        clearTaxesCache($current_company, 7, $year);
        clearTaxesCache($current_company, 8, $year);
        clearTaxesCache($current_company, 9, $year);
        clearTaxesCache($current_company, 10, $year);
        clearTaxesCache($current_company, 11, $year);
        clearTaxesCache($current_company, 12, $year);
        clearTaxesCache($current_company, 0, $year);
      	
    }
    
}

if (!function_exists('clearPermissionsCache')) {  

    function clearPermissionsCache($companyId, $userId) {
        
        $cacheKey = "cache-allow-$companyId-$userId";
        Cache::forget("$cacheKey-admin");
        Cache::forget("$cacheKey-invoicing");
        Cache::forget("$cacheKey-billing");
        Cache::forget("$cacheKey-validation");
        Cache::forget("$cacheKey-books");
        Cache::forget("$cacheKey-reports");
        Cache::forget("$cacheKey-catalogue");
        
    }

}

?>