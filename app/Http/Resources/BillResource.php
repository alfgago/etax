<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BillItemResource;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'clave'=> $this->document_key ,
            'codigoActividad'=> $this->commercial_activity ,
            'numeroConsecutivo'=> $this->document_number,
            'fechaEmision'=> $this->generated_date ,
            'fechaVencimiento'=> $this->due_date ,
            'emisor'=>[
                    'codigo'=> $this->provider->code ,
                    'nombre'=> $this->provider_first_name ,
                    'primerApellido'=> $this->provider_last_name ,
                    'segundoApellido'=> $this->provider_last_name2 ,
                    'identificacion'=> [
                        'tipo'=> $this->provider->getTipoPersonaXML(),
                        'numero'=> $this->provider_id_number
                    ],
                    'nombreComercial'=> $this->provider->fullname ,
                    'ubicacion'=> [
                        'pais'=> $this->provider_country ,
                        'provincia'=> $this->provider_state ,
                        'canton'=> $this->provider_city ,
                        'distrito'=> $this->provider_district ,
                        'barrio'=> '',
                        'codigoPostal'=> $this->provider_zip ,
                        'otrasSenas'=> $this->provider_address ,
                        'direccionExtranjero'=> ''
                    ],
                    'telefono'=> [
                        'codigoPais'=> $this->provider->phone_area,
                        'numTelefono'=> $this->provider_phone
                    ],
                    'correoElectronico'=> $this->provider_email ,
                    'exento'=> ''
                ] ,
            'receptor'=> [
                    'codigo'=> $this->company->code,
                    'nombre'=> $this->company->name ,
                    'primerApellido'=> $this->company->last_name ,
                    'segundoApellido'=> $this->company->last_name2 ,
                    'identificacion'=> [
                        'tipo'=> $this->company->getTipoPersonaXML(),
                        'numero'=> $this->company->id_number
                    ],
                    'nombreComercial'=> $this->company->business_name,
                    'ubicacion'=> [
                        'pais'=> $this->company->country ,
                        'provincia'=> $this->company->state ,
                        'canton'=> $this->company->city ,
                        'distrito'=> $this->company->district ,
                        'barrio'=> $this->company->neighborhood,
                        'codigoPostal'=> $this->company->zip ,
                        'otrasSenas'=> $this->company->address ,
                        'direccionExtranjero'=> ''
                    ],
                    'telefono'=> [
                        'codigoPais'=> '',
                        'numTelefono'=> $this->company->phone
                    ],
                    'correoElectronico'=> $this->company->email 
                ] ,
            'condicionVenta'=> $this->sale_condition ,
            'medioPago'=> $this->payment_type ,
            'tiempoCredito'=> $this->credit_time ,
            'ordenCompra'=> $this->buy_order ,
            'referencia'=> $this->other_reference,
            'moneda'=> $this->currency ,
            'tipoCambio'=> $this->currency_rate ,
            'descripcion'=> $this->description ,
            'aceptada'=> $this->hacienda_status ,
            'tipoXml'=> $this->xml_schema,
            'detalleServicio' => BillItemResource::collection($this->items),
            'resumenFactura'=>[
                    'totalServGravados' => $this->total_serv_gravados ,
                    'totalServExentos' => $this->total_serv_exentos ,
                    'totalMercanciaGravadas' => $this->total_merc_gravados ,
                    'totalMercanciaExentas' => $this->total_merc_exentas,
                    'totalGravado' => $this->total_gravado ,
                    'totalExento' => $this->total_exento ,
                    'totalVenta' => $this->total_venta ,
                    'totalDescuentos' => $this->total_descuento ,
                    'totalVentaNeta' => $this->total_venta_neta ,
                    'totalImpuesto' => $this->total_iva ,
                    'totalComprobante' => $this->total_comprobante
                ]
        ];
    }
}
