<?php

namespace EmizorIpx\PrepagoBags\Models;

use EmizorIpx\PrepagoBags\Services\PostpagoPlanCompanyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostpagoPlanCompany extends Model
{
    protected $table = 'postpago_plan_companies';

    protected $guarded = [];

    public function service(){
        return new PostpagoPlanCompanyService($this);
    }

}
