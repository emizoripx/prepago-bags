<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrepagoBagResource extends JsonResource
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
            "id" => $this->id,
            "nombre" => $this->name,
            "numeroFacturas" => $this->number_invoices,
            "frecuencia" => $this->frequency,
            "acumulativo" => $this->acumulative
        ];
    }
}
