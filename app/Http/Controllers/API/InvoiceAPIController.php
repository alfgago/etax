<?php
/**
 * Created by PhpStorm.
 * User: xavierpernalete
 * Date: 3/30/20
 * Time: 5:11 PM
 */

namespace App\Http\Controllers\API;


use App\CalculatedTax;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Invoice;
use App\ModelFilters\InvoiceFilter;
use App\Utils\BridgeHaciendaApi;
use App\Utils\ParametersValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class InvoiceAPIController extends Controller
{
    use ParametersValidator;

    /**
     * Envía la factura electrónica a Hacienda
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emitir(Request $request)
    {
        //revisar si el usuario tiene permisos sobre esa compania
        $user = auth()->user();
        if(!userHasCompany($request->emisor['identificacion']['numero'])) {
            return $this->createResponse(403, 'ERROR', "Empresa no autorizada", []);
        }
        $validator = $this->validatorInvoiceEmitir($request);
        if ($validator->fails()) {
            return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
        }

        //revision de branch para segmentacion de funcionalidades por tipo de documento
        try {
            Log::info("Envio de factura a hacienda -> ".json_encode($request->all()));

            //validar si la compania tiene facturas disponibles.
            $company = Cache::get("cache-api-company-".$request->emisor['identificacion']['numero']."-$user->id");

            $errors = $company->validateCompany(true);

            if($errors){
                return $this->createResponse($errors['codigo'], 'ERROR', $errors['mensaje'], []);
            }

            //valida si el mes esta cerrado
            if(CalculatedTax::validarMes($request->fechaEmision, $company)) {
                $apiHacienda = new BridgeHaciendaApi();
                $tokenApi = $apiHacienda->login(false);

                if ($tokenApi !== false) {
                    $invoice = new Invoice();
                    //$company = currentCompanyModel();
                    $invoice->company_id = $company->id;

                    //Datos generales y para Hacienda
                    $invoice->document_type = $request->tipo_documento;
                    $invoice->hacienda_status = '01';
                    $invoice->payment_status = "01";
                    $invoice->payment_receipt = "";
                    $invoice->generation_method = "etax-api";
                    $invoice->xml_schema = 43;
                    if ($request->tipo_documento == '01') {
                        $invoice->reference_number = $company->last_invoice_ref_number + 1;
                    }
                    if ($request->tipo_documento == '08') {
                        $invoice->reference_number = $company->last_invoice_pur_ref_number + 1;
                    }
                    if ($request->tipo_documento == '09') {
                        $invoice->reference_number = $company->last_invoice_exp_ref_number + 1;
                    }
                    if ($request->tipo_documento == '04') {
                        $invoice->reference_number = $company->last_ticket_ref_number + 1;
                    }

                    $invoiceData = $invoice->setInvoiceDataApi($request);
                    if(!isset($invoiceData)){
                        return $this->createResponse(400, 'ERROR', "Ha ocurrido un error al generar las lineas de la factura.", []);
                    }

                    if (!$request->clave && !$request->numeroConsecutivo) {
                        $invoice->document_key = $this->getDocumentKey($request->tipo_documento);
                        $invoice->document_number = $this->getDocReference($request->tipo_documento);
                    } else {
                        $invoice->reference_number = ltrim(substr($request->numeroConsecutivo,-10), '0');
                        $invoice->document_key = $request->clave;
                        $invoice->document_number = $request->numeroConsecutivo;
                    }

                    $invoice->save();

                    if (!empty($invoiceData)) {
                        $invoice = $apiHacienda->createInvoice($invoiceData, $tokenApi);
                    }

                    if ($request->tipo_documento == '01') {
                        $company->last_invoice_ref_number = $invoice->reference_number;
                        $company->last_document = $invoice->document_number;
                        $company->save();

                    } elseif ($request->tipo_documento == '08') {
                        $company->last_invoice_pur_ref_number = $invoice->reference_number;
                        $company->last_document_invoice_pur = $invoice->document_number;

                    } elseif ($request->tipo_documento == '09') {
                        $company->last_invoice_exp_ref_number = $invoice->reference_number;
                        $company->last_document_invoice_exp = $invoice->document_number;
                    } elseif ($request->tipo_documento == '04') {
                        $company->last_ticket_ref_number = $invoice->reference_number;
                        $company->last_document_ticket = $invoice->document_number;
                    }

                    $company->save();
                    clearInvoiceCache($invoice);
                    Cache::forget("cache-api-company-".$request->emisor['identificacion']['numero']."-$user->id");
                    Cache::forget("cache-has-company-".$request->emisor['identificacion']['numero']."-$user->id");

                    $response['clave_documento'] = $invoice->document_key;

                    return $this->createResponse(201, 'SUCCESS', 'Factura registrada y enviada con éxito', $response);
                } else {
                    return $this->createResponse(400, 'ERROR', "Ha ocurrido un error al enviar factura.", []);
                }
            }else{
                return $this->createResponse(400, 'ERROR', "El mes ya fue cerrado", []);
            }
        } catch( \Exception $ex ) {
            Log::error("ERROR Envio de factura a hacienda -> ".$ex);
            return $this->createResponse(400, 'ERROR', 'Ha ocurrido un error al enviar factura.'.$ex->getMessage(), []);
        }


    }

    /**
     * Envía nota de credito electrónica a Hacienda
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function anularInvoice($key, Request $request) {
        //revisar si el usuario tiene permisos sobre esa compania
        $user = auth()->user();
        if(!userHasCompany($request->emisor['identificacion']['numero'])) {
            return $this->createResponse(403, 'ERROR', "Empresa no autorizada", []);
        }

        $validator = $this->validatorInvoiceEmitir($request);
        if ($validator->fails()) {
            return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
        }

        try {

            Log::info('Enviando nota de credito de facturar -->'.$key);
            //validar si la compania tiene facturas disponibles.
            $company = Cache::get("cache-api-company-".$request->emisor['identificacion']['numero']."-$user->id");
            $invoice = Invoice::where('document_key', $key)->where('company_id', $company->id)->first();
            $errors = $company->validateCompany(true);

            if($errors) {
                return $this->createResponse($errors['codigo'], 'ERROR', $errors['mensaje'], []);
            }

            if(CalculatedTax::validarMes($request->fechaEmision, $company)) {
                $apiHacienda = new BridgeHaciendaApi();
                $tokenApi = $apiHacienda->login(false);
                if ($tokenApi !== false) {

                    $note = new Invoice();
                    //Datos generales y para Hacienda
                    $note->company_id = $company->id;
                    $note->document_type = "03";
                    $note->hacienda_status = '01';
                    $note->payment_status = "01";
                    $note->payment_receipt = "";
                    $note->generation_method = "etax";
                    $note->reason = $request->razon ?? "Anular Factura";
                    $note->code_note = $request->codigo_nota ?? "01";
                    $note->reference_number = $company->last_note_ref_number + 1;
                    $note->save();
                    $noteData = $note->setNoteDataApi($invoice, $note->document_type, $request);

                    if (!$request->clave && !$request->numeroConsecutivo) {
                        $note->document_key = $this->getDocumentKey($request->tipo_documento);
                        $note->document_number = $this->getDocReference($request->tipo_documento);
                    } else {
                        $note->reference_number = ltrim(substr($request->numeroConsecutivo,-10), '0');
                        $note->document_key = $request->clave;
                        $note->document_number = $request->numeroConsecutivo;
                    }

                    $note->save();

                    if (!empty($noteData)) {
                        $apiHacienda->createCreditNote($noteData, $tokenApi);
                    }
                    $company->last_note_ref_number = $noteData->reference_number;
                    $company->last_document_note = $noteData->document_number;
                    $company->save();

                    clearInvoiceCache($invoice);
                    Cache::forget("cache-api-company-".$request->emisor['identificacion']['numero']."-$user->id");
                    Cache::forget("cache-has-company-".$request->emisor['identificacion']['numero']."-$user->id");

                    $response['clave_documento'] = $invoice->document_key;

                    return $this->createResponse(201, 'SUCCESS', 'Nota de Credito registrada y enviada con éxito', $response);
                } else {
                    return $this->createResponse(400, 'ERROR', "Ha ocurrido un error al enviar nota de debito.", []);
                }
            } else {
                return $this->createResponse(400, 'ERROR', "El mes ya fue cerrado", []);
            }

        } catch ( \Exception $e) {
            Log::error("ERROR Envio de notaa de credito a hacienda -> ".$e);
            return $this->createResponse(400, 'ERROR', 'Ha ocurrido un error al enviar nota de debito.'.$e->getMessage(), []);
        }

    }

    public function sendNotaDebito($key, Request $request)
    {
        //revisar si el usuario tiene permisos sobre esa compania
        $user = auth()->user();
        if(!userHasCompany($request->emisor['identificacion']['numero'])) {
            return $this->createResponse(403, 'ERROR', "Empresa no autorizada", []);
        }

        $validator = $this->validatorInvoiceEmitir($request);
        if ($validator->fails()) {
            return $this->createResponse('400', 'ERROR' ,  $validator->messages(),[]);
        }
        try {
            Log::info('Enviando nota de credito de facturar -->'.$key);
            //validar si la compania tiene facturas disponibles.
            $company = Cache::get("cache-api-company-".$request->emisor['identificacion']['numero']."-$user->id");
            $invoice = Invoice::where('document_key', $key)->where('company_id', $company->id)->first();
            $errors = $company->validateCompany(true);

            if($errors) {
                return $this->createResponse($errors['codigo'], 'ERROR', $errors['mensaje'], []);
            }

            if(CalculatedTax::validarMes($request->fechaEmision, $company)) {
                $apiHacienda = new BridgeHaciendaApi();
                $tokenApi = $apiHacienda->login(false);
                if ($tokenApi !== false) {
                    $note = new Invoice();

                    //Datos generales y para Hacienda
                    $note->company_id = $company->id;
                    $note->document_type = "02";
                    $note->hacienda_status = '01';
                    $note->payment_status = "01";
                    $note->payment_receipt = "";
                    $note->generation_method = "etax";
                    $note->reason = $request->razon ?? "Debito Factura";
                    $note->code_note = $request->codigo_nota ?? "01";
                    $note->reference_number = $company->last_debit_note_ref_number + 1;
                    $note->save();
                    $noteData = $note->setNoteDataApi($invoice, $note->document_type, $request);
                    if (!empty($noteData)) {
                        $apiHacienda->createCreditNote($noteData, $tokenApi);
                    }
                    $company->last_debit_note_ref_number = $noteData->reference_number;
                    $company->last_document_debit_note = $noteData->document_number;
                    $company->save();

                    clearInvoiceCache($invoice);
                    $response['clave_documento'] = $invoice->document_key;

                    return $this->createResponse(201, 'SUCCESS', 'Nota de Debito registrada y enviada con éxito', $response);

                } else {
                    return $this->createResponse(400, 'ERROR', "Ha ocurrido un error al enviar nota de debito.", []);
                }
            }else{
                return $this->createResponse(400, 'ERROR', "El mes ya fue cerrado", []);
            }

        } catch ( \Exception $e) {
            Log::error("ERROR Envio de notaa de credito a hacienda -> ".$e);
            return $this->createResponse(400, 'ERROR', 'Ha ocurrido un error al enviar nota de debito.'.$e->getMessage(), []);
        }

    }


    /**
     * @OA\Get(
     *     path="/api/v1/facturas-venta/consultar-hacienda",
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
            return $this->createResponse('400', 'ERROR', 'Limite de consultas sobrepasado.');
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
            $invoice = Invoice::where('company_id', $company->id)
                        ->where('document_key', $request->clave)
                        ->first();
                        
            //La idea de esto es que no puedan enviar spam de requests            
            $throttleKey = "api-consultar-throttle-$request->empresa-$user->id";
            if ( Cache::has($throttleKey) ) {
                return $this->createResponse('400', 'ERROR', 'Limite: No se permite enviar mas de una solicitud de consulta cada 5 segundos.');
            }
            Cache::put($throttleKey, true, 5);
            
            if ($tokenApi !== false && !empty($invoice->xmlHacienda->xml) && !empty($invoice)) {
                $result = $apiHacienda->queryHacienda($invoice, $tokenApi, $company, false);
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

    private function getDocumentKey($docType) {
        $company = currentCompanyModel();
        $invoice = new Invoice();
        if ($docType == '01') {
            $ref = $company->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $ref = $company->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $ref = $company->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '04') {
            $ref = $company->last_ticket_ref_number + 1;
        }
        $key = '506'.$invoice->shortDate().$invoice->getIdFormat($company->id_number).self::getDocReference($docType).
            '1'.$invoice->getHashFromRef($ref);

        return $key;
    }

    private function getDocReference($docType) {
        if ($docType == '01') {
            $lastSale = currentCompanyModel()->last_invoice_ref_number + 1;
        }
        if ($docType == '08') {
            $lastSale = currentCompanyModel()->last_invoice_pur_ref_number + 1;
        }
        if ($docType == '09') {
            $lastSale = currentCompanyModel()->last_invoice_exp_ref_number + 1;
        }
        if ($docType == '04') {
            $lastSale = currentCompanyModel()->last_ticket_ref_number + 1;
        }
        $consecutive = "001"."00001".$docType.substr("0000000000".$lastSale, -10);

        return $consecutive;
    }

    public function getInvoice(Request $request) {

        if (!userHasCompany($request->empresa)) {
            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }

        $invoice = Invoice::where('document_key',$request->claveDocumento)->first();
        if (!$invoice) {
            return $this->createResponse('400', 'ERROR' , 'Factura no encontrada.');
        }
        return $this->createResponse('200', 'OK' , 'La factura '. $invoice->document_number . '.', new InvoiceResource($invoice));
    }

    public function listInvoice(Request $request){
        if(!userHasCompany($request->empresa)){
            return $this->createResponse('400', 'ERROR' , 'Empresa no autorizada.');
        }

        $invoice_filter = InvoiceFilter::class;
        $invoices = Invoice::filter($request->all(), $invoice_filter)->paginate($request->cantidad);
        return $this->createResponse('200', 'OK' , 'Todas las facturas.', InvoiceResource::collection($invoices), $invoices);
    }

}
