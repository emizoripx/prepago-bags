<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\ResourceCollection;
use EmizorIpx\ClientFel\Http\Resources\BranchResource;

class AccountDetailResource extends ResourceCollection
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
            "id" => $this->encodePrimaryKey($this->resource['id']),
            "invoice_number_available" => $this->resource['invoice_number_available'],
            "duedate" => strtotime($this->resource['duedate']),
            "acumulative" => boolval($this->resource['acumulative']),
            "production" => boolval($this->resource['production']),
            "is_postpago" => boolval($this->resource['is_postpago']),
            "invoice_counter" => $this->resource['invoice_counter'],
            "enabled" => $this->resource['enabled'],
            "phase" => $this->resource['phase'],
            "ruex" => $this->resource['ruex'],
            "nim" => $this->resource['nim'],
            "branches" => BranchResource::collection($this->resource['fel_branches']),
        ];
    }
}
