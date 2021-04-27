<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDocumentSectorResource extends JsonResource
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
            "id" => $this->resource['id'],
            "invoice_number_available" => $this->resource['invoice_number_available'],
            "document_sector" => TypeDocumentSector::getName($this->resource['fel_doc_sector_id']),
            "acumulative" => $this->resource['accumulative'],
            "duedate" => $this->resource['duedate'],
            "fel_doc_sector_code" => $this->resource['fel_doc_sector_id'],
            "counter" => $this->resource['counter'],
        ];
    }
}
