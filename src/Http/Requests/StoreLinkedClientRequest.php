<?php

namespace EmizorIpx\PrepagoBags\Http\Requests;

use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckAcumulativeBag;
use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckCompanyProduction;
use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckDecimals;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLinkedClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => [
                'required',
                new CheckCompanyProduction()
            ],
            'company_client_id' => 'required'
        ];
    }

    
}
