<?php

namespace EmizorIpx\PrepagoBags\Observers;

use EmizorIpx\ClientFel\Repository\FelProductRepository;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;

class FelClientCompanyObserver
{
    protected $repo;

    public function __construct(AccountPrepagoBagsRepository $repo)
    {
        $this->repo = $repo;
    }
    public function created($model)
    {
        
        $this->repo->updateCounterClients($model->company_id);
    }

}
