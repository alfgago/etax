<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'compras'=> $this->cc_compras ?? '',
            'importaciones'=> $this->cc_importaciones ?? '',
            'propiedades'=> $this->cc_propiedades ?? '',
            'iva_compras'=> $this->cc_iva_compras ?? '',
            'iva_importaciones'=> $this->cc_iva_importaciones ?? '',
            'iva_propiedades'=> $this->cc_iva_propiedades ?? '',
            'compras_sin_derecho'=> $this->cc_compras_sin_derecho ?? '',
            'proveedores_credito'=> $this->cc_proveedores_credito ?? '',
            'proveedores_contado'=> $this->cc_proveedores_contado ?? '',
            'ventas_1'=> $this->cc_ventas_1 ?? '',
            'ventas_2'=> $this->cc_ventas_2 ?? '',
            'ventas_4'=> $this->cc_ventas_4 ?? '',
            'ventas_13'=> $this->cc_ventas_13 ?? '',
            'ventas_exp'=> $this->cc_ventas_exp ?? '',
            'ventas_estado'=> $this->cc_ventas_estado ?? '',
            'ventas_1_iva'=> $this->cc_ventas_1_iva ?? '',
            'ventas_2_iva'=> $this->cc_ventas_2_iva ?? '',
            'ventas_4_iva'=> $this->cc_ventas_4_iva ?? '',
            'ventas_13_iva'=> $this->cc_ventas_13_iva ?? '',
            'ventas_sin_derecho'=> $this->cc_ventas_sin_derecho ?? '',
            'ventas_sum'=> $this->cc_ventas_sum ?? '',
            'clientes_credito'=> $this->cc_clientes_credito ?? '',
            'clientes_contado'=> $this->cc_clientes_contado ?? '',
            'clientes_credito_exp'=> $this->cc_clientes_credito_exp ?? '',
            'clientes_contado_exp'=> $this->cc_clientes_contado_exp ?? '',
            'clientes_sum'=> $this->cc_clientes_sum ?? '',
            'retenido'=> $this->cc_retenido ?? '',
            'bienes_capital_1'=> $this->cc_ppp_1 ?? '',
            'bienes_capital_2'=> $this->cc_ppp_2 ?? '',
            'bienes_capital_4'=> $this->cc_ppp_4 ?? '',
            'bienes_capital_13'=> $this->cc_ppp_3 ?? '',
            'bienes_servicios_1'=> $this->cc_bs_1 ?? '',
            'bienes_servicios_2'=> $this->cc_bs_2 ?? '',
            'bienes_servicios_4'=> $this->cc_bs_4 ?? '',
            'bienes_servicios_13'=> $this->cc_bs_3 ?? '',
            'iva_emitido_1'=> $this->cc_iva_emitido_1 ?? '',
            'iva_emitido_2'=> $this->cc_iva_emitido_2 ?? '',
            'iva_emitido_4'=> $this->cc_iva_emitido_4 ?? '',
            'iva_emitido_13'=> $this->cc_iva_emitido_3 ?? '',
            'ajuste_bienes_capital_1'=> $this->cc_aj_ppp_1 ?? '',
            'ajuste_bienes_capital_2'=> $this->cc_aj_ppp_2 ?? '',
            'ajuste_bienes_capital_3'=> $this->cc_aj_ppp_3 ?? '',
            'ajuste_bienes_capital_4'=> $this->cc_aj_ppp_4 ?? '',
            'ajuste_bienes_servicios_1'=> $this->cc_aj_bs_1 ?? '',
            'ajuste_bienes_servicios_2'=> $this->cc_aj_bs_2 ?? '',
            'ajuste_bienes_servicios_4'=> $this->cc_aj_bs_4 ?? '',
            'ajuste_bienes_servicios_13'=> $this->cc_aj_bs_3 ?? '',
            'ajuste_bienes_capital'=> $this->cc_ajuste_ppp ?? '',
            'ajuste_bienes_servicios'=> $this->cc_ajuste_bs ?? '',
            'gasto_no_acreditable'=> $this->cc_gasto_no_acreditable ?? '',
            'por_pagar'=> $this->cc_por_pagar ?? '',
            'suma_ajuste_debe'=> $this->cc_sum1 ?? '',
            'suma_ajuste_haber'=> $this->cc_sum2 ?? '',
            'compras1'=> $this->cc_compras1 ?? '',
            'compras2'=> $this->cc_compras2 ?? '',
            'compras4'=> $this->cc_compras4 ?? '',
            'compras13'=> $this->cc_compras3 ?? '',
            'importaciones1'=> $this->cc_importaciones1 ?? '',
            'importaciones2'=> $this->cc_importaciones2 ?? '',
            'importaciones4'=> $this->cc_importaciones4 ?? '',
            'importaciones13'=> $this->cc_importaciones3 ?? '',
            'propiedades1'=> $this->cc_propiedades1 ?? '',
            'propiedades2'=> $this->cc_propiedades2 ?? '',
            'propiedades4'=> $this->cc_propiedades4 ?? '',
            'propiedades13'=> $this->cc_propiedades3 ?? '',
            'iva_compras1'=> $this->cc_iva_compras1 ?? '',
            'iva_compras2'=> $this->cc_iva_compras2 ?? '',
            'iva_compras4'=> $this->cc_iva_compras4 ?? '',
            'iva_compras13'=> $this->cc_iva_compras3 ?? '',
            'iva_importaciones1'=> $this->cc_iva_importaciones1 ?? '',
            'iva_importaciones2'=> $this->cc_iva_importaciones2 ?? '',
            'iva_importaciones4'=> $this->cc_iva_importaciones4 ?? '',
            'iva_importaciones13'=> $this->cc_iva_importaciones3 ?? '',
            'iva_propiedades1'=> $this->cc_iva_propiedades1 ?? '',
            'iva_propiedades2'=> $this->cc_iva_propiedades2 ?? '',
            'iva_propiedades4'=> $this->cc_iva_propiedades4 ?? '',
            'iva_propiedades13'=> $this->cc_iva_propiedades3 ?? '',
            'compras_exentas'=> $this->cc_compras_exentas ?? '',
            'compras_sum'=> $this->cc_compras_sum ?? '',
            'ventas_canasta'=> $this->cc_ventas_canasta ?? '',
            'ventas_exentas'=> $this->cc_ventas_exentas ?? '',
            'ventas_aduana'=> $this->cc_ventas_aduana ?? ''
        ];
    }
}
