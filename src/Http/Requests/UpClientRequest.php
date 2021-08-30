<?php

namespace EmizorIpx\PrepagoBags\Http\Requests;

use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckCompanyUp;
use Illuminate\Foundation\Http\FormRequest;

class UpClientRequest extends FormRequest
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
                new CheckCompanyUp()
            ]
        ];
    }

    
}
