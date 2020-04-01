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
use App\Invoice;
use App\Utils\BridgeHaciendaApi;
use App\Utils\ParametersValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
}
