<?php


namespace EmizorIpx\PrepagoBags\Exceptions;

use Exception;

class PrepagoBagsException extends Exception{

    public function __construct($msg)
    {
        $finalMessage = 'Errors';

        if($msg != null){
            $finalMessage = $msg;
        }

        parent::__construct($finalMessage);
    }
}