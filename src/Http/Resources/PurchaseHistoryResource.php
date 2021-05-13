<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseHistoryResource extends JsonResource
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
            "amount" => floatval($this->amount),
            "number_invoices_before" => $this->number_invoices_before,
            "purchase_date" => strtotime($this->purchase_date)
        ];
    }
}
