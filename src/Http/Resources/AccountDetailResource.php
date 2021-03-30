<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountDetailResource extends JsonResource
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
            "invoice_number_available" => $this->invoice_number_available,
            "duedate" => strtotime($this->duedate),
            "acumulative" => boolval($this->acumulative),
            "production" => boolval($this->production),
            "is_postpago" => boolval($this->is_postpago),
            "invoice_counter" => $this->invoice_counter,
            "enabled" => $this->enabled,
            "phase" => $this->phase
        ];
    }
}
