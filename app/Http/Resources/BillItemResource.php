<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillItemResource extends JsonResource
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
                "lineaDetalles" => [
                    "numeroLinea" => $this->item_number,
                    "codigoComercial"=> [
                        "tipo"=> $this->product_type ,
                        "codigo"=> $this->code
                    ],
                    "cantidad"=> $this->item_count ,
                    "unidadMedida"=> $this->measure_unit ,
                    "detalle"=> $this->name ,
                    "precioUnitario"=> $this->unit_price ,
                    "montoTotal"=> $this->total ,
                    "subTotal"=> $this->subtotal ,
                    "impuesto"=> [
                        "codigo"=> '',
                        "codigoTarifa"=> $this->iva_type ,
                        "tarifa"=> $this->iva_percentage ,
                        "monto"=> $this->iva_amount ,
                        "exoneracion"=> [
                            "tipoDocumento"=> $this->exoneration_document_type ,
                            "numeroDocumento"=> $this->exoneration_document_number ,
                            "nombreInstitucion"=> $this->exoneration_company_name ,
                            "fechaEmision"=> $this->exoneration_date ,
                            "porcentajeExoneracion"=> $this->exoneration_porcent ,
                            "montoExoneracion"=> $this->exoneration_amount
                        ]
                    ],
                    "impuestoNeto"=> $this->impuesto_neto ,
                    "montoTotalLinea"=> $this->exoneration_total_amount 
                ]
            ];
    }
}
