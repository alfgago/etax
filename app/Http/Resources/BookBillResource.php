<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookBillResource extends JsonResource
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
            'TipoDocumento'=> $this->bill->documentTypeName() ?? '',
            'Fecha'=> $this->bill->generatedDate()->format('d/m/Y') ?? '',
            'Proveedor'=> $this->bill->providerName() ?? '',
            'Actividad'=> $this->bill->activity_company_verification ?? ($this->bill->commercial_activity ?? 'No indica') ?? '',
            'Consecutivo'=> $this->bill->document_number ?? '',
            'NumLinea'=> $this->item_number ?? '',
            'Producto'=> $this->name ?? '',
            'TipoIva'=> $this->ivaType ? $this->ivaType->name : 'No indica',
            'CategoriaDeclaracion'=> isset($map->productCategory) ?  ($this->productCategory->id . " - " . $this->productCategory->name) : 'No indica categoria',
            'Moneda'=> $this->bill->currency ?? '',
            'TipoCambio'=> $this->bill->currency_rate ?? '',
            'Subtotal'=> round( $this->subtotal, 2) ?? '',
            'TarifaIVA'=> $this->iva_percentage.'%' ?? '',
            'MontoIVA'=> round( $this->iva_amount, 2) ?? '',
            'Total'=> round( $this->total * ( $this->bill->document_type != '03' ? 1 : -1 ) , 2) ?? ''
        ];
    }
}
