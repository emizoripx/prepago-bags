<?php

namespace EmizorIpx\PrepagoBags\Http\Resources;

use App\Utils\Traits\MakesHash;
use Illuminate\Http\Resources\Json\JsonResource;

class PostpagoPlanCompanyResource extends JsonResource
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
            "price" => $this->price,
            "num_invoices" => $this->num_invoices,
            "num_clients" => $this->num_clients,
            "num_products" => $this->num_products,
            "num_branches" => $this->num_branches,
            "num_users" => $this->num_users,
            "prorated_invoice" => $this->prorated_invoice,
            "prorated_clients" => $this->prorated_clients,
            "prorated_products" => $this->prorated_products,
            "prorated_branches" => $this->prorated_branches,
            "prorated_users" => $this->prorated_users,
            "frequency" => $this->frequency,
            "all_sector_docs" => boolval($this->all_sector_docs),
            "sector_doc_id" => $this->sector_doc_id,
            "enable_overflow" => boolval($this->enable_overflow),
            "company_id" => $this->company_id,
            "start_date" => $this->start_date,
            "postpago_exceded_amount" => $this->postpago_exceded_amount,
            "created_at" => strtotime($this->created_at),
        ];
    }
}
