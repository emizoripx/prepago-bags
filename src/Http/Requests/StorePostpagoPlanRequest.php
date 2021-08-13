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
        $rule = ['sector_doc_id' => 'integer'];
        if(request('all_sector_docs') != true){
            $rule['sector_doc_id'] = 'required|integer';
        }
        return array_merge( $rule, [
            'name' => 'required|string|unique:postpago_plans',
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
            'frequency' => 'required|integer',
            'all_sector_docs' => 'boolean',
            'sector_doc_id' => 'integer',
            'enable_overflow' => 'boolean',
            'public' => 'boolean'
        ]);
    }

    
}
