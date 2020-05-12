<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListaCierreResource extends JsonResource
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
            'id'=> number_format( $this->id, 0 ) ?? 0,
            'periodo'=> \App\Variables::getMonthName($this->month)." ".$this->year,
            'rectificacion' => $this->is_rectification ? '(Rectificación)' : '',
            'ventas'=> number_format( $this->invoices_subtotal, 0 ) ?? '',
            'Compras'=> number_format( $this->bills_subtotal, 0 ) ?? '',
            'IvaVenta'=> number_format( $this->total_invoice_iva, 0 ) ?? '',
            'IvaCompra'=> number_format( $this->total_bill_iva, 0 ) ?? '',
            'IvaAcreditable'=> number_format( $this->iva_deducible_operativo, 0 ) ?? '',
            'IvaPorPagar'=> $this->balance_operativo > 0 ? number_format( $this->balance_operativo, 0 ) : 0,
            'IvaPorCobrar'=> $this->balance_operativo < 0 ? number_format( abs($this->balance_operativo), 0 ) : 0,
            'Retencion'=> number_format($this->iva_retenido, 0 ) ?? '',
            'Estado'=> $this->is_closed ? 'Cerrado' : 'Abierto' ?? ''
        ];
    }
}
