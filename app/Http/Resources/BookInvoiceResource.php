<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookInvoiceResource extends JsonResource
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
            'TipoDocumento'=> $this->invoice->documentTypeName() ?? '',
            'Fecha'=> $this->invoice->generatedDate()->format('d/m/Y') ?? '',
            'Cliente'=> $this->invoice->clientName() ?? '',
            'Actividad'=> $this->invoice->activity_company_verification ?? ($this->invoice->commercial_activity ?? 'No indica') ?? '',
            'Consecutivo'=> $this->invoice->document_number ?? '',
            'NumLinea'=> $this->item_number ?? '',
            'Producto'=> $this->name ?? '',
            'TipoIva'=> $this->ivaType ? $this->ivaType->name : 'No indica',
            'CategoriaDeclaracion'=> isset($map->productCategory) ?  ($this->productCategory->id . " - " . $this->productCategory->name) : 'No indica categoria',
            'Moneda'=> $this->invoice->currency ?? '',
            'TipoCambio'=> $this->invoice->currency_rate ?? '',
            'Subtotal'=> round( $this->subtotal, 2) ?? '',
            'TarifaIVA'=> $this->iva_percentage.'%' ?? '',
            'MontoIVA'=> round( $this->iva_amount, 2) ?? '',
            'Total'=> round( $this->total * ( $this->invoice->document_type != '03' ? 1 : -1 ) , 2) ?? ''
        ];
    }
}
