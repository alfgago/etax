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

?>