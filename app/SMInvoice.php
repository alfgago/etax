<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;
use App\Client;
use App\Invoice;
use App\InvoiceItem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SMInvoice extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    
    //Relacion con la factura
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    /*
    	1.1 Compras ByS Locales	B
        1. S Acreditable	1.2 importación ByS Exterior	S
        2. Sin IVA S / Con IVA S N Acreditable	2.1 Bienes y Servicios Exentos	L
        2.2 Bienes y Servicios N Sujetos	I
	    2.3 Bienes y Servicios N Relac Actividad	

    
    */
    public static function parseFormatoSM($codigo) {
        
    $cacheCodigoSM = "cachekey-codigossm-$codigo";
    if ( Cache::has($cacheCodigoSM) ) {    
        return Cache::get($cacheCodigoSM);
    }
        
    $lista = [
        //Sin identificación específica
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B001 -  Compras locales de bienes con IVA al 1% sin identificación específica', 'codigo'=>'B001', 'porcentaje'=>'1', 'is_bienes'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B002 -  Compras locales de bienes con IVA al 2% sin identificación específica', 'codigo'=>'B002', 'porcentaje'=>'2', 'is_bienes'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B003 -  Compras locales de bienes con IVA al 13% sin identificación específica ', 'codigo'=>'B003', 'porcentaje'=>'13', 'is_bienes'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B004 -  Compras locales de bienes con IVA al 4% sin identificación específica', 'codigo'=>'B004', 'porcentaje'=>'4', 'is_bienes'=>true],
        
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B011 -  Compras locales de bienes de capital con IVA al 1% sin identificación específica', 'codigo'=>'B011', 'porcentaje'=>'1', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B012 -  Compras locales de bienes de capital con IVA al 2% sin identificación específica', 'codigo'=>'B012', 'porcentaje'=>'2', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B013 -  Compras locales de bienes de capital con IVA al 13% sin identificación específica', 'codigo'=>'B013', 'porcentaje'=>'13', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B014 -  Compras locales de bienes de capital con IVA al 4% sin identificación específica', 'codigo'=>'B014', 'porcentaje'=>'4', 'is_servicio'=>true, 'hide'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B015 -  Compras locales de bienes de capital con IVA al 1% sin identificación específica. Con costo superior a 15 salarios base', 'codigo'=>'B015', 'porcentaje'=>'1', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : No Identificable  - - - - -  B016 -  Compras locales de bienes de capital con IVA al 13% sin identificación específica. Con costo superior a 15 salarios base', 'codigo'=>'B016', 'porcentaje'=>'13', 'is_servicio'=>true],
        
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B021 -  Importaciones de bienes con IVA al 1% sin identificación específica', 'codigo'=>'B021', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B022 -  Importaciones de bienes con IVA al 2% sin identificación específica', 'codigo'=>'B022', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B023 -  Importaciones de bienes con IVA al 13% sin identificación específica', 'codigo'=>'B023', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B024 -  Importaciones de bienes con IVA al 4% sin identificación específica', 'codigo'=>'B024', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_importacion'=>true, 'hide'=>true],
        
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B031 -  Importaciones de bienes de capital con IVA al 1% sin identificación específica', 'codigo'=>'B031', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B032 -  Importaciones de bienes de capital con IVA al 2% sin identificación específica', 'codigo'=>'B032', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B033 -  Importaciones de bienes de capital con IVA al 13% sin identificación específica', 'codigo'=>'B033', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B034 -  Importaciones de bienes de capital con IVA al 4% sin identificación específica', 'codigo'=>'B034', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true, 'hide'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B035 -  Importaciones de bienes de capital con IVA al 1% sin identificación específica Con costo superior a 15 salarios base', 'codigo'=>'B035', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : No Identificable  - - - - -  B036 -  Importaciones de bienes de capital con IVA al 13% sin identificación específica Con costo superior a 15 salarios base', 'codigo'=>'B036', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true],
        
        //Con identificación específica
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B040 -  Importaciones de bienes exentos', 'codigo'=>'B040', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B041 -  Importaciones con IVA al 1% de bienes de acreditación plena con identificación específica', 'codigo'=>'B041', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B042 -  Importaciones con IVA al 2% de bienes de acreditación plena con identificación específica', 'codigo'=>'B042', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B043 -  Importaciones con IVA al 13% de bienes de acreditación plena con identificación específica', 'codigo'=>'B043', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B044 -  Importaciones con IVA al 4% de bienes de acreditación plena con identificación específica', 'codigo'=>'B044', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true, 'hide'=>true],
        
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B050 -  Importaciones de bienes de capital exentos', 'codigo'=>'B050', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B051 -  Importaciones con IVA al 1% de bienes de capital de acreditación plena con identificación específica', 'codigo'=>'B051', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B052 -  Importaciones con IVA al 2% de bienes de capital de acreditación plena con identificación específica', 'codigo'=>'B052', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B053 -  Importaciones con IVA al 13% de bienes de capital de acreditación plena con identificación específica', 'codigo'=>'B053', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : B : Identificable  - - - - -  B054 -  Importaciones con IVA al 4% de bienes de capital de acreditación plena con identificación específica', 'codigo'=>'B054', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true, 'hide'=>true],
        
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B060 -  Compras locales de bienes y servicios exentos y no sujetos.', 'codigo'=>'B060', 'porcentaje'=>'0', 'is_bienes'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B061 -  Compras locales con IVA al 1% de bienes de acreditación plena con identificación específica', 'codigo'=>'B061', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B062 -  Compras locales con IVA al 2% de bienes de acreditación plena con identificación específica', 'codigo'=>'B062', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B063 -  Compras locales con IVA al 13% de bienes de acreditación plena con identificación específica', 'codigo'=>'B063', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B064 -  Compras locales con IVA al 4% de bienes de acreditación plena con identificación específica', 'codigo'=>'B064', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_identificacion_plena'=>true],

        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B070 -  Compras locales de  bienes de capital exentos.', 'codigo'=>'B070', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B071 -  Compras locales con IVA al 1% de bienes de capital de acreditación plena con identificación específica.', 'codigo'=>'B071', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B072 -  Compras locales con IVA al 2% de bienes de capital de acreditación plena con identificación específica.', 'codigo'=>'B072', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B073 -  Compras locales con IVA al 13% de bienes de capital de acreditación plena con identificación específica.', 'codigo'=>'B073', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : B : Identificable  - - - - -  B074 -  Compras locales con IVA al 4% de bienes de capital de acreditación plena con identificación específica.', 'codigo'=>'B074', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_identificacion_plena'=>true, 'hide'=>true],
        
        //No acreditables
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  B080 -  Compras de bienes con IVA no acreditable desde origen', 'codigo'=>'B080', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_gravado'=>false],
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : I  - - - - -  B090 -  Importaciones de bienes con IVA no acreditable desde origen', 'codigo'=>'B090', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_gravado'=>false],
        ['descripcion'=>'2.3 Bienes y Servicios N Relac Actividad : B : L  - - - - -  B097 -  Compras de bienes con IVA no acreditable por gastos no deducibles', 'codigo'=>'B097', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_gravado'=>false],
        
        //Sin identificación específica
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : No Identificable  - - - - -  S001 -  Compras locales de servicios con IVA al 1% sin identificación específica', 'codigo'=>'S001', 'porcentaje'=>'1', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : No Identificable  - - - - -  S002 -  Compras locales de servicios con IVA al 2% sin identificación específica', 'codigo'=>'S002', 'porcentaje'=>'2', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : No Identificable  - - - - -  S003 -  Compras locales de servicios con IVA al 13% sin identificación específica ', 'codigo'=>'S003', 'porcentaje'=>'13', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : No Identificable  - - - - -  S004 -  Compras locales de servicios con IVA al 4% sin identificación específica', 'codigo'=>'S004', 'porcentaje'=>'4', 'is_servicio'=>true],
        
        ['descripcion'=>'1.2 importación ByS Exterior             : S : No Identificable  - - - - -  S021 -  Importaciones de servicios con IVA al 1% sin identificación específica', 'codigo'=>'S021', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true, 'hide'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : No Identificable  - - - - -  S022 -  Importaciones de servicios con IVA al 2% sin identificación específica', 'codigo'=>'S022', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true, 'hide'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : No Identificable  - - - - -  S023 -  Importaciones de servicios con IVA al 13% sin identificación específica', 'codigo'=>'S023', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : No Identificable  - - - - -  S024 -  Importaciones de servicios con IVA al 4% sin identificación específica', 'codigo'=>'S024', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true, 'hide'=>true],

        //Con identificación específica
        ['descripcion'=>'2.1 Bienes y Servicios Exentos           : S : I  - - - - -  S040 -  Importaciones de servicios exentos', 'codigo'=>'S040', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_importacion'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : Identificable  - - - - -  S041 -  Importaciones con IVA al 1% de servicios de acreditación plena con identificación específica', 'codigo'=>'S041', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true, 'hide'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : Identificable  - - - - -  S042 -  Importaciones con IVA al 2% de servicios de acreditación plena con identificación específica', 'codigo'=>'S042', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true, 'hide'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : Identificable  - - - - -  S043 -  Importaciones con IVA al 13% de servicios de acreditación plena con identificación específica', 'codigo'=>'S043', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.2 importación ByS Exterior             : S : Identificable  - - - - -  S044 -  Importaciones con IVA al 4% de servicios de acreditación plena con identificación específica', 'codigo'=>'S044', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true, 'hide'=>true],
        
        ['descripcion'=>'2.1 Bienes y Servicios Exentos           : S : L  - - - - -  S060 -  Compras locales de servicios exentos', 'codigo'=>'S060', 'porcentaje'=>'0', 'is_servicio'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : Identificable  - - - - -  S061 -  Compras locales con IVA al 1% de servicios de acreditación plena con identificación específica', 'codigo'=>'S061', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : Identificable  - - - - -  S062 -  Compras locales con IVA al 2% de servicios de acreditación plena con identificación específica', 'codigo'=>'S062', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : Identificable  - - - - -  S063 -  Compras locales con IVA al 13% de servicios de acreditación plena con identificación específica', 'codigo'=>'S063', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
        ['descripcion'=>'1.1 Compras ByS Locales                  : S : Identificable  - - - - -  S064 -  Compras locales con IVA al 4% de servicios de acreditación plena con identificación específica', 'codigo'=>'S064', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_identificacion_plena'=>true],

        //No acreditables
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : S : L  - - - - -  S080 -  Compras de servicios con IVA no acreditable desde origen', 'codigo'=>'S080', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_gravado'=>false],
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : S : I  - - - - -  S090 -  Importaciones de servicios con IVA no acreditable desde origen', 'codigo'=>'S090', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_gravado'=>false],
        ['descripcion'=>'2.3 Bienes y Servicios N Relac Actividad : S : L  - - - - -  S097 -  Compras de servicios con IVA no acreditable por gastos no deducibles', 'codigo'=>'S097', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_gravado'=>false],
        
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  B091 -  Compras de bienes con IVA no acreditable desde origen', 'codigo'=>'B091', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  B092 -  Compras de bienes con IVA no acreditable desde origen', 'codigo'=>'B092', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  B093 -  Compras de bienes con IVA no acreditable desde origen', 'codigo'=>'B093', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  B094 -  Compras de bienes con IVA no acreditable desde origen', 'codigo'=>'B094', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_gravado'=>true],
        
        ['descripcion'=>' - R001 - Impuesto específico sobre Bebidas Gaseosas de fabricación Nacional', 'codigo'=>'R001', 'porcentaje'=>'13.688935', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>' - R002 - Impuesto específico sobre Bebidas Gaseosas Importadas', 'codigo'=>'R002', 'porcentaje'=>'13.688935', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>' - R003 - Impuesto específico sobre Cerveza', 'codigo'=>'R003', 'porcentaje'=>'14.45', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>' - R004 - Impuesto específico sobre Cigarrillos', 'codigo'=>'R004', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>' - R005 - Impuesto específico sobre Destilados (licores)', 'codigo'=>'R005', 'porcentaje'=>'17.2', 'is_bienes'=>true, 'is_gravado'=>true],
        ['descripcion'=>' - R006 - Impuesto específico sobre Destilados (licores Fanal)', 'codigo'=>'R006', 'porcentaje'=>'12.433', 'is_bienes'=>true, 'is_gravado'=>true],
        
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  098 -  Inversion del sujeto pasivo base', 'codigo'=>'098', 'porcentaje'=>'0', 'is_gravado'=>false, 'hide'=>true, 'hidden2018'=>true],
        ['descripcion'=>'2.2 Bienes y Servicios N Sujetos         : B : L  - - - - -  099 -  Inversion del sujeto pasivo base no acreditable', 'codigo'=>'099', 'porcentaje'=>'0', 'is_gravado'=>false, 'hide'=>true, 'hidden2018'=>true]
    ];
      
    foreach($lista as $it){
        if($it['codigo'] == $codigo){
            $descr = trim(preg_replace('/\s+/', ' ', $it['descripcion']));
            Cache::put($cacheCodigoSM, $descr, now()->addHours(24));
            return $descr;
        }
    }
    return $codigo;
  }
    
}
