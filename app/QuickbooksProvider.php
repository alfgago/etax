<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use QuickBooksOnline\API\Facades\Vendor as qbProvider;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use \Carbon\Carbon;
use App\Provider;
use App\Quickbooks;
use App\Company;

class QuickbooksProvider extends Model
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

    public function provider(){
        return $this->belongsTo(Provider::class);
    }
    
    public static function getMonthlyProviders($dataService, $year, $month, $company) {
        $cachekey = "qb-providers-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $count = QuickbooksProvider::where('company_id', $company->id)->count();
            //$count = 0;
            if( $count > 0 ){
                $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
                $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
                $providers = $dataService->Query("SELECT * FROM Vendor WHERE MetaData.LastUpdatedTime >= '$dateFrom' AND MetaData.LastUpdatedTime <= '$dateTo'");
            }else{
                $providers = $dataService->Query("SELECT * FROM Vendor");
            }
            Cache::put($cachekey, $providers, 30); //Cache por 15 segundos.
        }else{
            $providers = Cache::get($cachekey);
        }
        return $providers;
    }
    
    public static function syncMonthlyProviders($dataService, $year, $month, $company){
        $qbProviders = QuickbooksProvider::getMonthlyProviders($dataService, $year, $month, $company);
        $qbProviders = $qbProviders ?? [];
        $providers = [];

        foreach($qbProviders as $provider){
            $email = isset($provider->PrimaryEmailAddr) ? $provider->PrimaryEmailAddr->Address : 'No indica correo';
            $fullname = (($provider->DisplayName ?? $provider->FullyQualifiedName) ?? $provider->CompanyName);

            QuickbooksProvider::updateOrCreate(
                [
                    "qb_id" => $provider->Id
                  ],
                  [
                    "full_name" => $fullname ?? null,
                    "email" => $email,
                    "qb_data" => $provider,
                    "generated_at" => 'quickbooks',
                    "company_id" => $company->id
                  ]
            );
        }
    }
    
    public static function getProviderName($company, $vendorRef){
        $qbVendor = QuickbooksProvider::getProviderInfo($company, $vendorRef);
        
        if(isset($qbVendor)){
            return $qbVendor->full_name;
        }else{
            return $vendorRef;
        }
    }
    
    public static function getProviderInfo($company, $vendorRef){
        $cachekey = "qb-vendor-$company->id_number-$vendorRef";
        if ( !Cache::has($cachekey) ) {
            $qbVendor = QuickbooksProvider::where('company_id', $company->id)
                        ->where('qb_id', $vendorRef)
                        ->with('provider')
                        ->first();
            if( !isset($qbVendor) ){
                return false;
            }
            Cache::put($cachekey, $qbVendor, 30); //Cache por 15 segundos.
        }else{
            $qbVendor = Cache::get($cachekey);
        }
        return $qbVendor;
    }
    
    public function saveQbaetax(){   
        $data = $this->qb_data;
        if( isset($data['BillAddr']) ){
              try{
                $zip = $data['BillAddr']['PostalCode'] ?? '10101';
                $state = $zip[0];
                $city = $zip[1] . $zip[2];
                $district = $zip;
              }catch( \Throwable $e ){ }  
              $countryCode = $data['BillAddr']['CountryCode'] ?? 'CR';
              $address = $data['BillAddr']['City'] ?? "" . " " . $data['BillAddr']['Line1'] ?? "" . " " . $data['BillAddr']['Line2'] ?? "";
        }
        
        if( isset($data['PrimaryPhone']) ){
            $phone = $data['PrimaryPhone']["FreeFormNumber"];
        }
        $fullname = (($data['DisplayName'] ?? $data['FullyQualifiedName']) ?? $data['CompanyName']) ?? null;
        $newProvider = Provider::updateOrCreate(
            [
                'company_id' => $this->company_id,
                'email' => $this->email,
                'fullname' => $fullname
            ],
            [
                'id_number' => null,
                'tipo_persona' => null,
                'code' => $fullname,
                'first_name' => $data['GivenName'] ?? $fullname,
                'last_name' => $data['FamilyName'] ?? null,
                'last_name2' => null,
                'zip' => $zip ?? '10101',
                'country' => $countryCode ?? null,
                'state' => $state ?? null,
                'city' => $city ?? null,
                'district' => $district ?? null,
                'neighborhood' => null,
                'address' => $address ?? null,
                'foreign_address' => $address ?? null,
                'phone' => $phone ?? null
            ]
        );
       
        $this->provider_id = $newProvider->id;
        $this->save();
    }
    
    public static function saveEtaxaqb($dataService, $provider){   
        $fullname = ($provider->first_name ?? '') . ' ' . ($provider->last_name ?? '') . ' ' . ($provider->last_name2 ?? '');
        $email = 'No indica correo' != $provider->email ? $provider->email : null;
        $fullname = trim($fullname);
        
        $theResourceObj = qbProvider::create([
            "BillAddr" => [
                "Line1" => $provider->address ?? "",
                "City" => $provider->city ?? "",
                "CountryCode" => $provider->country ?? "CR",
                "Country" => $provider->country ?? "CR",
                "PostalCode" => $provider->zip ?? "",
            ],
            "GivenName" => $provider->first_name ?? '',
            "FamilyName" => $provider->last_name ?? '' . ' ' . $provider->last_name2 ?? '',
            "FullyQualifiedName" => $fullname,
            "CompanyName" => $fullname,
            "DisplayName" => $fullname,
            "PrintOnCheckName" => $provider->toString(),
            "PrimaryPhone" => [
                "FreeFormNumber" => $provider->phone
            ],
            "PrimaryEmailAddr" => [
                "Address" => $email
            ]
        ]);
        
        $vendor = $dataService->Add($theResourceObj);
        $error = $dataService->getLastError();
        if ($error) {
            Log::error("The Status code is: " . $error->getHttpStatusCode() . "\n".
                        "The Helper message is: " . $error->getOAuthHelperError() . "\n".
                        "The Response message is: " . $error->getResponseBody() . "\n");
            $vendors = $dataService->Query("SELECT * FROM Vendor WHERE CompanyName = '$fullname'");
            $vendor = $vendors[0] ?? null;
        }
        if( isset($vendor) ){
            $qbProvider = QuickbooksProvider::updateOrCreate(
                [
                    "qb_id" => $vendor->Id
                  ],
                  [
                    "full_name" => $fullname,
                    "email" => $provider->email,
                    "qb_data" => $vendor,
                    "generated_at" => 'etax',
                    "company_id" => $provider->company_id,
                    "provider_id" => $provider->id
                  ]
            );
            return $qbProvider;
        }
        return $error;
    }
}
