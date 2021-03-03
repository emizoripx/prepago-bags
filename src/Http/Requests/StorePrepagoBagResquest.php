<?php

namespace EmizorIpx\PrepagoBags\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrepagoBagResquest extends FormRequest
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
            'number_invoices' => 'required|integer',
            'name' => 'required|string',
            'frequency' => [
                'required',
                Rule::in(['monthly', 'yearly'])
            ],
            'acumulative' => 'required|bool',
            'amount' => 'required|numeric'
        ];
    }

    
}
