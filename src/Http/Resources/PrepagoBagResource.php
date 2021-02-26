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
            "id" => $this->encodePrimaryKey($this->id),
            "name" => $this->name,
            "number_invoices" => $this->number_invoices,
            "frequency" => $this->frequency,
            "acumulative" => boolval($this->acumulative),
            "created_at" => strtotime($this->created_at)
        ];
    }
}
