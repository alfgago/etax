<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OtherChargesResource extends JsonResource
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
                "numeroLinea" => $this->item_number,
                "tipoDocumento"=> $this->document_type ,
                "numeroIdentidadTercero"=> $this->provider_id_number ,
                "nombreTercero"=> $this->provider_name ,
                "detalle"=> $this->description ,
                "porcentaje"=> $this->percentage ,
                "montoCargo"=> $this->amount ,
        ];
    }
}
