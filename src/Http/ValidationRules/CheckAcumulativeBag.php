<?php


namespace EmizorIpx\PrepagoBags\Http\ValidationRules;

use Illuminate\Contracts\Validation\Rule;


class CheckAcumulativeBag implements Rule
{
    public $input;

    protected $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkProduct($value); //if it exists, return false!
    }

    /**
     * @return string
     */
    public function message()
    {
        return ctrans('Una Bolsa Gratis debe ser Acumulativa');
    }

    /**
     * @return bool
     */
    private function checkProduct($acumulative) : bool
    {
        if($this->amount == 0 && !$acumulative){
            return false;
        }

        return true;
    }
}
