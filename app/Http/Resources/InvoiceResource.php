<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
                'codigo'=> '' ,
                'nombre'=> $this->company->name ,
                'primerApellido'=> $this->company->last_name ,
                'segundoApellido'=> $this->company->last_name2 ,
                'identificacion'=> [
                    'tipo'=> $this->company->type,
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
                'correoElectronico'=> $this->company->email ,
                'exento'=> ''
            ] ,
            'receptor'=> [
                'codigo'=> '' ,
                'nombre'=> $this->client_first_name ,
                'primerApellido'=> $this->client_last_name ,
                'segundoApellido'=> $this->client_last_name2 ,
                'identificacion'=> [
                    'tipo'=> '' ,
                    'numero'=> $this->client_id_number
                ],
                'nombreComercial'=> '',
                'ubicacion'=> [
                    'pais'=> $this->client_country ,
                    'provincia'=> $this->client_state ,
                    'canton'=> $this->client_city ,
                    'distrito'=> $this->client_district ,
                    'barrio'=> '',
                    'codigoPostal'=> $this->client_zip ,
                    'otrasSenas'=> $this->client_address ,
                    'direccionExtranjero'=> ''
                ],
                'telefono'=> [
                    'codigoPais'=> '',
                    'numTelefono'=> $this->client_phone
                ],
                'correoElectronico'=> $this->client_email ,
                'exento'=> ''
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
            'detalleServicio' => InvoiceItemResource::collection($this->items),
            'otrosCargos' => OtherChargesResource::collection($this->otherCharges),
            'resumenFactura'=>[
                'totalServGravados' => $this->total_serv_gravados ,
                'totalServExentos' => $this->total_serv_exentos ,
                'totalServExonerados' => $this->total_serv_exonerados ,
                'totalMercanciaGravadas' => $this->total_merc_gravados ,
                'totalMercanciaExentas' => $this->total_merc_exentas,
                'totalMercanciaExonerados' => $this->total_merc_exonerados,
                'totalGravado' => $this->total_gravado ,
                'totalExento' => $this->total_exento ,
                'totalExonerados' => $this->total_exonerados ,
                'totalVenta' => $this->total_venta ,
                'totalDescuentos' => $this->total_descuento ,
                'totalVentaNeta' => $this->total_venta_neta ,
                'totalImpuesto' => $this->total_iva ,
                'totalOtrosCargos' => $this->total_otros_cargos,
                'totalComprobante' => $this->total_comprobante
            ]
        ];
    }
}
