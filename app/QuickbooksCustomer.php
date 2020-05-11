<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use QuickBooksOnline\API\Facades\Customer as qbCustomer;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use \Carbon\Carbon;
use App\Client;
use App\Quickbooks;
use App\Company;

class QuickbooksCustomer extends Model
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

    public function client(){
        return $this->belongsTo(Client::class);
    }
    
    public static function getMonthlyClients($dataService, $year, $month, $company) {
        $cachekey = "qb-clients-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $count = QuickbooksCustomer::where('company_id', $company->id)->count();
            if( $count > 0 ){
                $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
                $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
                $clients = $dataService->Query("SELECT * FROM Customer WHERE MetaData.LastUpdatedTime >= '$dateFrom' AND MetaData.LastUpdatedTime <= '$dateTo'");
            }else{
                $clients = $dataService->Query("SELECT * FROM Customer");
            }
            Cache::put($cachekey, $clients, 30); //Cache por 15 segundos.
        }else{
            $clients = Cache::get($cachekey);
        }
        return $clients;
    }
    
    public static function syncMonthlyClients($dataService, $year, $month, $company){
        $qbCustomers = QuickbooksCustomer::getMonthlyClients($dataService, $year, $month, $company);
        $qbCustomers = $qbCustomers ?? [];
        $clientes = [];

        foreach($qbCustomers as $customer){
            $email = isset($customer->PrimaryEmailAddr) ? $customer->PrimaryEmailAddr->Address : 'No indica correo';
            $fullname = (($customer->DisplayName ?? $customer->FullyQualifiedName) ?? $customer->CompanyName) ?? null;
            QuickbooksCustomer::updateOrCreate(
                [
                    "qb_id" => $customer->Id,
                    "company_id" => $company->id
                  ],
                  [
                    "full_name" => $fullname ?? null,
                    "email" => $email,
                    "qb_data" => $customer,
                    "generated_at" => 'quickbooks',
                  ]
            );
        }
    }
    
    public static function getClientName($company, $customerRef){
        $qbCustomer = QuickbooksCustomer::getClientInfo($company, $customerRef);
        
        if(isset($qbCustomer)){
            return $qbCustomer->full_name;
        }else{
            return $customerRef;
        }
    }
    
    public static function getClientInfo($company, $customerRef){
        $cachekey = "qb-customer-$company->id_number-$customerRef";
        if ( !Cache::has($cachekey) ) {
            $qbCustomer = QuickbooksCustomer::where('company_id', $company->id)
                        ->where('qb_id', $customerRef)
                        ->with('client')
                        ->first();
            if( !isset($qbCustomer) ){
                return false;
            }
            Cache::put($cachekey, $qbCustomer, 30); //Cache por 15 segundos.
        }else{
            $qbCustomer = Cache::get($cachekey);
        }
        return $qbCustomer;
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
        $newClient = Client::updateOrCreate(
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
       
        $this->client_id = $newClient->id;
        $this->save();
    }
    
    public static function saveEtaxaqb($dataService, $client, $update = null){   
        $fullname = ($client->first_name ?? '') . ' ' . ($client->last_name ?? '') . ' ' . ($client->last_name2 ?? '');
        $fullname = trim($fullname);
        
        $resourceObjectArray = [
            "BillAddr" => [
                "Line1" => $client->address ?? "",
                "City" => $client->city ?? "",
                "CountryCode" => $client->country ?? "CR",
                "Country" => $client->country ?? "CR",
                "PostalCode" => $client->zip ?? "",
            ],
            "GivenName" => $client->first_name ?? '',
            "FamilyName" => $client->last_name ?? '' . ' ' . $client->last_name2 ?? '',
            "FullyQualifiedName" => $fullname,
            "CompanyName" => $fullname,
            "DisplayName" => $fullname,
            "PrintOnCheckName" => $client->toString(),
            "PrimaryPhone" => [
                "FreeFormNumber" => $client->phone
            ],
            "PrimaryEmailAddr" => [
                "Address" => $client->email
            ]
        ];
        
        if($update){
            $existingCustomer = $dataService->FindbyId('customer', $update);
            $theResourceObj = qbCustomer::update( $existingCustomer,
                $resourceObjectArray
            );
            $customer = $dataService->Update($theResourceObj);
        }else{
            $theResourceObj = qbCustomer::create(
                $resourceObjectArray
            );
            $customer = $dataService->Add($theResourceObj);
        }
        
        $error = $dataService->getLastError();
        if ($error) {
            Log::error("The Status code is: " . $error->getHttpStatusCode() . "\n".
                        "The Helper message is: " . $error->getOAuthHelperError() . "\n".
                        "The Response message is: " . $error->getResponseBody() . "\n");
            $customers = $dataService->Query("SELECT * FROM Customer WHERE CompanyName = '$fullname'");
            $customer = $customers[0] ?? null;
        }
        if( isset($customer) ){
            $qbCustomer = QuickbooksCustomer::updateOrCreate(
                [
                    "qb_id" => $customer->Id,
                    "company_id" => $client->company_id
                  ],
                  [
                    "full_name" => $customer->FullyQualifiedName,
                    "email" => $client->email,
                    "qb_data" => $customer,
                    "generated_at" => 'etax',
                    "client_id" => $client->id
                  ]
            );
            return $qbCustomer;
        }
        return $error;
    }
}
