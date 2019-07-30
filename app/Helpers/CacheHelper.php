<?php

if (!function_exists('clearInvoiceCache')) {

    function clearInvoiceCache($invoice){
        $month = $invoice->month;
        $year = $invoice->year;
        clearTaxesCache($invoice->company_id, $month, $year);
        clearTaxesCache($invoice->company_id, 0, $year);
        
        $userId = auth()->user()->id;
        Cache::forget("cache-currentcompany-$userId");
        
    }
    
}

if (!function_exists('clearBillCache')) {
	
    function clearBillCache($bill){
        $month = $bill->month;
        $year = $bill->year;
        clearTaxesCache($bill->company_id, $month, $year);
        clearTaxesCache($bill->company_id, 0, $year);
        
        $userId = auth()->user()->id;
        Cache::forget("cache-currentcompany-$userId");
    }
    
}

if (!function_exists('clearTaxesCache')) {  
	
    function clearTaxesCache($current_company, $month, $year){
      	$cacheKey = "cache-taxes-$current_company-$month-$year";
      	Cache::forget($cacheKey);
    }
    
}
    
if (!function_exists('clearCierreCache')) {    
	
    function clearCierreCache($current_company, $month, $year){
      $cacheKey = "cache-estadoCierre-$current_company-$month-$year";
      Cache::forget($cacheKey);
    }
    
}

if (!function_exists('clearLastTaxesCache')) {  
	
    function clearLastTaxesCache($current_company, $anoAnterior) {
      	$cacheKey = "cache-lasttaxes-$current_company-0-$anoAnterior";
      	Cache::forget($cacheKey);
      	
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