<?php

namespace EmizorIpx\PrepagoBags\Http\ValidationRules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CheckDecimals implements Rule
{
    private $maxIntegers;
    private $maxDecimals;

    public function __construct($maxIntegers = 20, $maxDecimals = 5)
    {
        $this->maxIntegers = $maxIntegers;
        $this->maxDecimals = $maxDecimals;
    }

    public function passes($attribute, $value)
    {
        // $value = sprintf('%.20f', $value);
        // Log::debug('value = ' . $value); // TODO: Fix parsing from 0.00001 to 1.0e-5
        $regex ="/^[0-9]{1,$this->maxIntegers}+(\.[0-9]{1,$this->maxDecimals})?$/";
        return preg_match($regex, $value);
    }

    public function message()
    {
        return 'El campo debe tener como maximo '.$this->maxIntegers.' digitos y '. $this->maxDecimals.' decimales';
    }
}
