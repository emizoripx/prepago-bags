<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class PrepagoBagResource extends JsonResource
{

    use MakesHash;
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
            "amount" => (float) $this->amount,
            "created_at" => strtotime($this->created_at),
            "sector_document_type_code" => $this->sector_document_type_code
        ];
    }
}
