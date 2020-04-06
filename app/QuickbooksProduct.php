<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use QuickBooksOnline\API\Facades\Item as qbProduct;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use \Carbon\Carbon;
use App\Product;
use App\Quickbooks;
use App\Company;

class QuickbooksProduct extends Model
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

    public function product(){
        return $this->belongsTo(Product::class);
    }
    
    public static function getMonthlyProducts($dataService, $year, $month, $company) {
        $cachekey = "qb-products-$company->id_number-$year-$month"; 
        if ( !Cache::has($cachekey) ) {
            $count = QuickbooksProduct::where('company_id', $company->id)->count();
            $count = 0;
            if( $count > 0 ){
                $dateFrom = Carbon::createFromDate($year, $month, 1)->firstOfMonth()->toAtomString();
                $dateTo = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toAtomString();
                $products = $dataService->Query("SELECT * FROM Item WHERE MetaData.LastUpdatedTime >= '$dateFrom' AND MetaData.LastUpdatedTime <= '$dateTo'");
            }else{
                $products = $dataService->Query("SELECT * FROM Item");
            }
            Cache::put($cachekey, $products, 30); //Cache por 15 segundos.
        }else{
            $products = Cache::get($cachekey);
        }
        return $products;
    }
    
    public static function syncMonthlyProducts($dataService, $year, $month, $company){
        $qbProducts = QuickbooksProduct::getMonthlyProducts($dataService, $year, $month, $company);
        $qbProducts = $qbProducts ?? [];
        $products = [];
        foreach($qbProducts as $product){
            $subtotal = $product->UnitPrice;
            $fullname = $product->Name;
            $accountRef = $product->IncomeAccountRef;
            QuickbooksProduct::updateOrCreate(
                [
                    "qb_id" => $product->Id
                  ],
                  [
                    "name" => $fullname ?? null,
                    "subtotal" => $subtotal,
                    "qb_data" => $product,
                    "account_ref" => $accountRef,
                    "generated_at" => 'quickbooks',
                    "company_id" => $company->id
                  ]
            );
        }
    }
    
    public function saveQbaetax(){   
        $data = $this->qb_data;
        $subtotal = $data['UnitPrice'];
        $fullname = $data['Name'];
        $inventoryType = $data['Type'];
        $measureUnit = 'Unid';
        if($inventoryType == 'Service'){
            $defaultIveType = 'S103';
            $defaultProductCategory = '17';
            $measureUnit = 'Sp';
        }else{
            $defaultIveType = 'B103';
            $defaultProductCategory = '15';
        }
        $newProduct = Product::updateOrCreate(
            [
                'company_id' => $this->company_id,
                'name' => $fullname
            ],
            [
                'unit_price' => $subtotal,
                'code' => $fullname,
                'description' => $data['Description'] ?? null,
                'default_iva_type'  => $defaultIveType,
                'product_category_id'  => $defaultProductCategory,
                'is_catalogue' => true,
                'measure_unit' => $measureUnit
            ]
        );
       
        $this->product_id = $newProduct->id;
        $this->save();
        return $newProduct;
    }
    
    public static function saveEtaxaqb($dataService, $product, $accountRef = null){   
        $company = $product->company;
        $um = $product->measure_unit;
        if($um == 'Sp' || $um == 'Spe' || $um == 'St' || $um == 'Al' || $um == 'Alc' || $um == 'Cm' || $um == 'I' || $um == 'Os'){
            $inventoryType = 'Service';
        }else{
            $inventoryType = 'Service';
        }
        
        $accountRefArray = [
            "value" => $accountRef ?? 48    
        ];
        
        $theResourceObj = qbProduct::create([
            "Name" => $product->name,
            "Description" => $product->description,
            "UnitPrice" => $product->unit_price,
            "IncomeAccountRef" => $accountRefArray,
            "Type" => $inventoryType,
            "QtyOnHand" => 1,
            "InvStartDate" => $product->created_at->format('d/m/Y')
        ]);
        
        $item = $dataService->Add($theResourceObj);
        $error = $dataService->getLastError();
        if ($error) {
            Log::error("The Status code is: " . $error->getHttpStatusCode() . "\n".
                        "The Helper message is: " . $error->getOAuthHelperError() . "\n".
                        "The Response message is: " . $error->getResponseBody() . "\n");
            $items = $dataService->Query("SELECT * FROM Item WHERE Name = '$product->name'");
            $item = $items[0] ?? null;
        }
        if( isset($item) ){
            $qbProd = QuickbooksProduct::updateOrCreate(
                [
                    "qb_id" => $item->Id
                  ],
                  [
                    "name" => $product->name ?? null,
                    "subtotal" => $product->unit_price,
                    "qb_data" => $item,
                    "account_ref" => $accountRefArray['value'],
                    "generated_at" => 'quickbooks',
                    "product_id" => $product->id,
                    "company_id" => $product->company_id
                  ]
            );
            return $qbProd;
        }
        return $error;
    }
    
    public static function getProductByRef($company, $productRef){
        $cachekey = "qb-product-$company->id_number-$productRef";
        if ( !Cache::has($cachekey) ) {
            $qbProduct = QuickBooksProduct::where('company_id', $company->id)
                        ->where('qb_id', $productRef)
                        ->with('product')
                        ->first();
            if( !isset($qbProduct) ){
                $qbProduct = QuickbooksProduct::createProductFromRef($company, $productRef);
            }
            if( !isset($qbProduct->product) ){
                $qbProduct->saveQbaetax();
            }
            if( !isset($qbProduct->product) ){
                return false;
            }
            $etaxProduct = $qbProduct->product;
            Cache::put($cachekey,$etaxProduct, 30); //Cache por 15 segundos.
        }else{
            $etaxProduct = Cache::get($cachekey);
        }
        return $etaxProduct;
    }
    
    public static function createProductFromRef($company, $productRef){
        $qb = Quickbooks::where('company_id', $company->id)->with('company')->first();
        if( !isset($qb) ){
            return redirect('/quickbooks/configuracion')->withError('Usted no tiene Quickbooks configurado correctamente.');
        }
        $dataService = $qb->getAuthenticatedDS();
        $products = $dataService->Query("SELECT * FROM Item WHERE Id = '$productRef'");
        if($products){
            $product = $products[0];
            $subtotal = $product->UnitPrice;
            $fullname = $product->Name;
            $accountRef = $product->IncomeAccountRef;
            $qbProduct = QuickbooksProduct::updateOrCreate(
                [
                    "qb_id" => $product->Id
                  ],
                  [
                    "name" => $fullname ?? null,
                    "subtotal" => $subtotal,
                    "qb_data" => $product,
                    "account_ref" => $accountRef,
                    "generated_at" => 'quickbooks',
                    "company_id" => $company->id
                  ]
            );
            $qbProduct->save();
            return $qbProduct;
        }
        return false;
    }
    
    
    
}
