<?php

namespace EmizorIpx\PrepagoBags\Traits;

use Hashids\Hashids;

trait DecodeIdsTrait{

    public function getDecodeId($value){
        $hashids = new Hashids(config('ninja.hash_salt'), 10);
        \Log::debug('Decode');
        \Log::debug($hashids->decode($value));
        return $hashids->decode($value);
    }
}