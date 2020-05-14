<?php
/**
 * Created by PhpStorm.
 * User: xavierpernalete
 * Date: 5/12/20
 * Time: 12:42 PM
 */

namespace App\Http\Controllers\API;


use App\Bill;
use App\BillItem;
use App\CalculatedTax;
use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\ModelFilters\BillFilter;
use App\Utils\ParametersValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BillAPIController extends Controller
{
    use  ParametersValidator;

    /**
     * @OA\Post(
     *     path="/api/v1/facturas-compra/registrar",
     *     summary="Registra las facturas de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Factura de compra creada."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if(!userHasCompany($request['receptor']["identificacion"]["numero"])){

            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }
        $validator = $this->validatorBillStore($request);
        if ($validator->fails()) {
            return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
        }

        $company = Cache::get("cache-api-company-".$request['receptor']['identificacion']['numero']."-$user->id");

        if(substr($request->clave, 9, 12) != str_pad($request->emisor['identificacion']['numero'], 12, "0",STR_PAD_LEFT)){
            return $this->createResponse('400', 'ERROR' , 'La clave del documento no pertenece al proveedor.');
        }
        if(!$this->validatePersonType($request['emisor']["identificacion"]["tipo"])){
            return $this->createResponse('400', 'ERROR' , 'Tipo identificación del emisor es erroneo.');
        }
        if(CalculatedTax::validarMes($request->fechaEmision,$company)){
            $bill = new Bill();
            $bill->company_id = $company->id;
            //Datos generales y para Hacienda
            $bill->document_type = "01";
            $bill->hacienda_status = "03";
            $bill->status = "02";
            $bill->payment_status = "01";
            $bill->payment_receipt = "";
            $bill->generation_method = "A";
            $bill->reference_number = $company->last_bill_ref_number + 1;
            $bill->accept_status = 1;
            $bill->setBillData($request);
            $company->last_bill_ref_number = $bill->reference_number;
            $company->save();
            clearBillCache($bill);
            return $this->createResponse('201', 'OK' , 'Factura de compra creada.', new BillResource($bill));
        }else{
            return $this->createResponse('400', 'ERROR' , 'Mes seleccionado ya fue cerrado.');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/facturas-compra/aceptar",
     *     summary="Aceptar o rechazar facturas de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Aceptacion enviada."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function sendAcceptMessage (Request $request)
    {
        try {
            $user = auth()->user();
            if (!userHasCompany($request->empresa)) {

                return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
            }
            $validator = $this->validatorBillAccept($request);
            if ($validator->fails()) {
                return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
            }
            $company = Cache::get("cache-api-company-".$request->empresa."-$user->id");
            $bill = Bill::where('document_key',$request->claveDocumento)->where('company_id',$company->id)->first();
            if(!$bill){
                return $this->createResponse('400', 'ERROR' , 'Factura no encontrada.');
            }
            if (currentCompanyModel()->use_invoicing) {
                $apiHacienda = new BridgeHaciendaApi();
                $tokenApi = $apiHacienda->login(false);
                if ($tokenApi !== false) {
                    if (!empty($bill)) {
                        $bill->accept_status = $request->aceptacion;
                        $bill->save();
                        $company->last_rec_ref_number = $company->last_rec_ref_number + 1;
                        $company->save();
                        $company->last_document_rec = getDocReference('05',$company->last_rec_ref_number);
                        $company->save();
                        $apiHacienda->acceptInvoice($bill, $tokenApi);
                    }
                    $mensaje = 'Aceptacion enviada.';
                    if($request->aceptacion == 2){
                        $mensaje = 'Rechazo de factura enviado';
                    }
                    clearBillCache($bill);
                    return $this->createResponse('201', 'OK' ,  $mensaje ,new BillResource($bill));

                } else {
                    return $this->createResponse('400', 'ERROR' ,  'Ha ocurrido un error al enviar factura.',[]);
                }
            } else {
                if (!empty($bill)) {
                    $bill->accept_status = $request->respuesta;
                    $bill->save();
                }
                clearBillCache($bill);
                return $this->createResponse('201', 'OK' ,  'Factura aceptada para cálculo en eTax.',new BillResource($bill));
            }
        } catch ( Exception $e) {
            Log::error ("Error al crear aceptacion de factura");
            return $this->createResponse('400', 'ERROR' ,  'La factura no pudo ser aceptada. Por favor contáctenos.',[]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/facturas-compra/validar",
     *     summary="Validar una factura de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Aceptacion enviada."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function validateBill(Request $request)
    {
        $user = auth()->user();
        if(!userHasCompany($request->empresa)){
            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }

        $validator = $this->validatorBillValidate($request);

        if ($validator->fails()) {
            return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
        }

        $company = Cache::get("cache-api-company-".$request->empresa."-$user->id");

        if (!$this->validateCommercialActivities($request)) {
            return $this->createResponse('400', 'ERROR' , 'Actividad comercial no asignada a esta empresa.');
        }

        $bill = Bill::where('document_key',$request->claveDocumento)->where('company_id',$company->id)->first();
        if(!$bill){
            return $this->createResponse('400', 'ERROR' , 'Factura no encontrada.');
        }

        if(CalculatedTax::validarMes($bill->generatedDate(),$company)){
            $bill->activity_company_verification = $request->actividadComercial;
            $bill->is_code_validated = true;
            foreach( $request->lineas as $item ) {
                BillItem::where('id', $item['idLinea'])
                    ->update([
                        'iva_type' =>  $item['tipoIva'],
                        'product_type' =>  $item['tipoProducto'],
                        'porc_identificacion_plena' =>  $item['pocentajeIdentificacionPlena']
                    ]);
            }

            $bill->save();

            clearBillCache($bill);

            return $this->createResponse('200', 'OK' , 'La factura '. $bill->document_key . ' ha sido validada.',new BillResource($bill));
        }else{
            return $this->createResponse('400', 'ERROR' , 'Mes seleccionado ya fue cerrado.');
        }

    }

    /**
     * @OA\Get(
     *     path="/api/v1/facturas-compra/autorizar",
     *     summary="autorizar una factura de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve una factura de compra."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function authorizeBill ( Request $request)
    {
        $user = auth()->user();
        if(!userHasCompany($request->empresa)){
            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }

        $company = Cache::get("cache-api-company-$request->empresa"."-$user->id");
        $bill = Bill::where('document_key',$request->claveDocumento)->where('company_id',$company->id)->first();
        if(CalculatedTax::validarMes( $bill->generatedDate(),$company )){

            if ( $request->autorizar ) {
                $bill->is_authorized = true;
                $bill->save();
                clearBillCache($bill);

                return $this->createResponse('200', 'OK' , 'La factura '. $bill->document_key . 'ha sido autorizada. Recuerde validar el código.');
            }else {
                $bill->is_authorized = false;
                $bill->is_void = true;
                BillItem::where('bill_id', $bill->id)->delete();
                $bill->delete();
                clearBillCache($bill);

                return $this->createResponse('200', 'OK' , 'La factura '. $bill->document_key . 'ha sido rechazada');
            }

        }else{
            return $this->createResponse('400', 'ERROR' , 'Mes seleccionado ya fue cerrado.');
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/facturas-compra/consultar",
     *     summary="Consulta de una factura de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve una factura de compra."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function getBill(Request $request) {
        $user = auth()->user();
        $validator = $this->validatorgetBill($request);
        if ($validator->fails()) {
            return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
        }
        if(!userHasCompany($request->empresa)){
            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }
        $company = Cache::get("cache-api-company-".$request->empresa."-$user->id");
        $bill = Bill::where('document_key',$request->claveDocumento)->where('company_id',$company->id)->first();
        if(!$bill) {
            return $this->createResponse('400', 'ERROR' , 'Factura no encontrada.');
        }
        return $this->createResponse('200', 'OK' , 'La factura '. $bill->document_key . '.', new BillResource($bill));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/facturas-compra/consultar-hacienda",
     *     summary="Consulta de una factura de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve una factura de compra."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function consultarHacienda(Request $request) {
        try {
            //revisar si el usuario tiene permisos sobre esa compania
            $user = auth()->user();
            Log::info("Consultando factura");
            if(!userHasCompany($request->empresa)) {
                return $this->createResponse(403, 'ERROR', "Empresa no autorizada", []);
            }
            $validator = $this->validatorConsultarFactura($request);
            if ($validator->fails()) {
                return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
            }
            //validar si la compania tiene facturas disponibles.
            $company = Cache::get("cache-api-company-$request->empresa-$user->id");
            $apiHacienda = new BridgeHaciendaApi();
            $tokenApi = $apiHacienda->login(false);
            $invoice = Bill::where('document_key', $request->clave)->first();

            if ($tokenApi !== false && !empty($invoice->xmlHacienda->xml) && !empty($invoice)) {
                $result = $apiHacienda->queryHacienda($invoice, $tokenApi, $company);
                if ($result == false) {
                    $response['xml_mensaje_hacienda'] = 'El servidor de Hacienda es inaccesible en este momento, o el 
                        comprobante no ha sido recibido. Por favor intente de nuevo más tarde o contacte a soporte.';
                } else {
                    $response['xml_mensaje_hacienda'] = $result;
                }
                $response['xml_factura'] = Storage::get($invoice->xmlHacienda->xml);
                $response['estado_hacienda'] = $invoice->hacienda_status;
                return $this->createResponse(200, 'SUCCESS', 'Factura encontrada', $response);
            } else {
                return $this->createResponse(400, 'ERROR', 'Factura no encontrada', []);
            }

        } catch (\Exception $e) {
            Log::error("Error al consultar factura $e");
            return $this->createResponse(400, 'ERROR', 'Ha ocurrido un error al consultar 
                factura.'.$e->getMessage(), []);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/v1/facturas-compra/lista",
     *     summary="Lista de las facturas de compra",
     *     tags={"Facturas de compra"},
     *     @OA\Response(
     *         response=200,
     *         description="Devuelve una lista de facturas de compra."
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="ERROR"
     *     )
     * )
     */
    public function listBill(Request $request) {
        $user = auth()->user();
        if(!userHasCompany($request->empresa)){
            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }
        $company = Cache::get("cache-api-company-".$request->empresa."-$user->id");
        $bill_filter = BillFilter::class;
        $bills = Bill::filter($request->all(), $bill_filter)->paginate($request->cantidad);

        return $this->createResponse('200', 'OK' , 'Todas las facturas.',BillResource::collection($bills),$bills);
    }

}
