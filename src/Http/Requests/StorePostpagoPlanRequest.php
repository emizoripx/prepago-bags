<?php

namespace EmizorIpx\PrepagoBags\Http\Requests;

use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckAcumulativeBag;
use EmizorIpx\PrepagoBags\Http\ValidationRules\CheckDecimals;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostpagoPlanRequest extends FormRequest
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
        \Log::debug("Validation");
        \Log::debug(request());
        $rule = ['sector_doc_id' => 'integer'];
        $all_sectos_docs = request('all_sector_docs');
        if( !isset($all_sectos_docs) ){
            $rule['sector_doc_id'] = 'required|integer';
        }
        return array_merge( $rule, [
            'name' => [
                'required',
                'string',
                Rule::unique('postpago_plans')->ignore(request('id'))
            ],
            'price' => [
                'required',
                new CheckDecimals(7,2)
            ],
            'num_invoices' => 'integer',
            'num_clients' => 'integer',
            'num_products' => 'integer',
            'num_branches' => 'integer',
            'num_users' => 'integer',
            'prorated_invoice' => [
                'numeric',
                new CheckDecimals(5,2)
            ],
            'prorated_clients' => [
                'numeric',
                new CheckDecimals(5,2)
            ],
            'prorated_products' => [
                'numeric',
                new CheckDecimals(5,2)
            ],
            'prorated_branches' => [
                'numeric',
                new CheckDecimals(5,2)
            ],
            'prorated_users' => [
                'numeric',
                new CheckDecimals(5,2)
            ],
            'frequency' => 'integer',
            'all_sector_docs' => 'boolean',
            'enable_overflow' => 'boolean',
            'public' => 'boolean'
        ]);
    }

    
}
