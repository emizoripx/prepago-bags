<?php

namespace EmizorIpx\PrepagoBags\Observers;

use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
use Exception;

class FelUserCompanyObserver
{
    protected $repo;

    public function __construct(AccountPrepagoBagsRepository $repo)
    {
        $this->repo = $repo;
    }
    public function created($model)
    {
        
        try{
            if($model->getCompany())
                $this->repo->updateCounterUsers($model->company()->id) ;
        } catch(Exception $ex){
            \Log::debug("Error al Obtener la compañia: ". $ex->getMessage());
        }
    }

}
