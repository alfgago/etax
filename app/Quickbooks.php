<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Quickbooks;
use App\Client;
use App\Provider;

class Quickbooks extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $table = "quickbooks";

    protected $casts = [
        'conditions_json' => 'array',
        'payment_methods_json' => 'array',
        'taxes_json' => 'array',
        'clients_json' => 'array',
        'providers_json' => 'array',
        'products_json' => 'array',
    ];
    
    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    } 
    
    public function getAuthenticationUrl(){
        
        $dataService = $this->getDataService();
        
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        //dd($authorizationCodeUrl);
        return $authorizationCodeUrl;
    }
    
    public function registerQuickbooks($company, $authorizationCode, $RealmID){
        $dataService = $this->getDataService();
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        
        $authenticator = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($authorizationCode, $RealmID);
        $accessTokenValue  = $authenticator->getAccessToken();
        $refreshTokenValue  = $authenticator->getRefreshToken();
        
        $qb = Quickbooks::updateOrCreate(
            [
                'company_id' => $company->id
            ],[
                'authorization_code' => encrypt($authorizationCode),
                'realm_id'           => encrypt($RealmID),
                'access_token'       => encrypt($accessTokenValue),
                'refresh_token'      => encrypt($refreshTokenValue)
            ]
        );
        
        Log::info("Nueva autorización con Quickbooks $company->id_number");
        
        return $qb;
    }
    
    public function getAuthenticatedDS(){
        $company = $this->company;
        $cachekey = "qb-ds-$company->id_number"; 
        if ( !Cache::has($cachekey) ) {
            $refreshToken = decrypt($this->refresh_token);
            $realmId = decrypt($this->realm_id);
            //The first parameter of OAuth2LoginHelper is the ClientID, second parameter is the client Secret
            $oauth2LoginHelper = new OAuth2LoginHelper(config('etax.qb_client_id'), config('etax.qb_client_secret'));
            $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($refreshToken);
            $accessTokenValue = $accessTokenObj->getAccessToken();
            $refreshTokenValue = $accessTokenObj->getRefreshToken();
            
            $this->access_token = encrypt($accessTokenValue);
            $this->refresh_token = encrypt($refreshTokenValue);
            $this->save();
            
            // Prep Data Services
            $dataService = DataService::Configure(array(
                 'auth_mode' => 'oauth2',
                  'ClientID' => config('etax.qb_client_id'),
                  'ClientSecret' => config('etax.qb_client_secret'),
                 'scope' => "com.intuit.quickbooks.accounting, openID, profile, email, phone, address",
                 'baseUrl' => "https://sandbox-quickbooks.api.intuit.com/",
                 'QBORealmID' => $realmId,
                 'accessTokenKey' => $accessTokenValue,
                 'refreshTokenKey' => $refreshTokenValue
            ));
            return $dataService;
            Cache::put($cachekey, $dataService, 3600);
        }
        
        return Cache::get($cachekey);
    }
    
    private function getDataService(){
        // Prep Data Services
        $dataService = DataService::Configure(array(
              'auth_mode' => 'oauth2',
              'ClientID' => config('etax.qb_client_id'),
              'ClientSecret' => config('etax.qb_client_secret'),
              'RedirectURI' => config('etax.qb_redirect_uri'),
              'scope' => "com.intuit.quickbooks.accounting",
              'baseUrl' => "https://sandbox-quickbooks.api.intuit.com/"
        ));
        
        return $dataService;
    }
    
    public static function getClientName($company, $customerRef){
        $cachekey = "qb-clientsjson-$company->id_number";
        if ( !Cache::has($cachekey) ) {
            $qbclients = Quickbooks::select('clients_json')->where('company_id', $company->id)->with('company')->first();
            $customers = $qbclients->clients_json;
            Cache::put($cachekey, $customers, 30); //Cache por 15 segundos.
        }else{
            $customers = Cache::get($cachekey);
        }
        
        $cust = $customers[$customerRef];
        if(isset($cust)){
            return $cust['full_name'];
        }else{
            return $customerRef;
        }
    }
    
    public static function getClientInfo($company, $customerRef){
        $cachekey = "qb-clientsjson-$company->id_number";
        if ( !Cache::has($cachekey) ) {
            $qbclients = Quickbooks::select('clients_json')->where('company_id', $company->id)->with('company')->first();
            $customers = $qbclients->clients_json;
            Cache::put($cachekey, $customers, 30); //Cache por 15 segundos.
        }else{
            $customers = Cache::get($cachekey);
        }
        
        $clientInfo = [];
        $cust = $customers[$customerRef];
        if(isset($cust)){
            $client = Client::find($cust['client_id']); 
            if( isset($client) ){
                $clientInfo['nombreCliente'] = $client->fullname;
                $clientInfo['codigoCliente'] = $client->code;
                $clientInfo['tipoPersona'] = $client->tipo_persona;
                if($client->tipo_persona){
                    Log::error("Enviando factura de cliente no mapeado");
                    return false;  
                }
                $clientInfo['identificacionCliente'] = $client->id_number ?? null;
                $clientInfo['correoCliente'] = $client->email ?? null;
                $clientInfo['telefonoCliente'] = $client->phone ?? null;
                $clientInfo['direccion'] = $client->address ?? "No indica";
                $clientInfo['codProvincia'] = $client->state ?? "1";
                $clientInfo['codCanton'] = $client->city ?? "01";
                $clientInfo['codDistrito'] = $client->district ?? "01";
                $clientInfo['zip'] = $clientInfo['codProvincia'].$clientInfo['codCanton'].$clientInfo['codDistrito'];
            }else{
                Log::error("Enviando factura de cliente no mapeado");
                return false;
            }
        }else{
            return false;
        }
        
        return $clientInfo;
    }
    
    public function getAccounts($company = false){
        if(!$company){
            $company = currentCompanyModel();
        }
        
        $cachekey = "qb-clientsjson-$company->id_number";
        if ( !Cache::has($cachekey) ) {
            $qbAccounts = $this->getAuthenticatedDS()->Query("SELECT * FROM Account");
            Cache::put($cachekey, $qbAccounts, 600); //Cache por 10 minutos.
        }else{
            $qbAccounts = Cache::get($cachekey);
        }
        return $qbAccounts;
    }
    
}
