<?php


namespace EmizorIpx\PrepagoBags\Http\ValidationRules;

use Illuminate\Contracts\Validation\Rule;
use DB;

class CheckCompanyUp implements Rule
{

    public function __construct()
    {
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkCompany($value); //if it exists, return false!
    }

    /**
     * @return string
     */
    public function message()
    {
        return ctrans('La Compañia no se encuentra suspendida');
    }

    /**
     * @return bool
     */
    private function checkCompany($company_id) : bool
    {
        $company = DB::table('fel_company')->where('company_id', $company_id)->select('enabled')->first();
        if($company->enabled == 'inactive'){
            return true;
        }

        return false;
    }
}
