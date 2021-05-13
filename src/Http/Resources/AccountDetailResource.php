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
            "production" => boolval($this->resource['production']),
            "is_postpago" => boolval($this->resource['is_postpago']),
            "enabled" => $this->resource['enabled'],
            "phase" => $this->resource['phase'],
            "ruex" => $this->resource['ruex'],
            "nim" => $this->resource['nim'],
            "client_id" => $this->resource['fel_company_token']['client_id'],
            "client_secret" => substr_replace($this->resource['fel_company_token']['client_secret'],'***************', 5, -20),
            "settings_integration" => json_decode($this->resource['settings']),
            "branches" => BranchResource::collection($this->resource['fel_branches']),
            "document_sector_detail" => CompanyDocumentSectorResource::collection($this->resource['fel_company_document_sector'])
        ];
    }
}
