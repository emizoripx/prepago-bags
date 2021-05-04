<?php

namespace EmizorIpx\PrepagoBags\Http\Requests;

use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckAcumulativeBag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrepagoBagRequest extends FormRequest
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
        \Log::debug(request()->amount);
        return [
            'number_invoices' => 'required|integer',
            'name' => 'required|string',
            'frequency' => [
                'required',
                Rule::in(['monthly', 'yearly'])
            ],
            'amount' => 'required|numeric',
            'acumulative' => [
                'required',
                'bool',
                new CheckAcumulativeBag(request()->amount)
            ],
            'sector_document_type_code' => 'nullable|integer'
        ];
    }

    
}
