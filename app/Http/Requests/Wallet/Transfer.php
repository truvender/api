<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class Transfer extends FormRequest
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
            'amount' => 'required|numeric|min:'.config('truvender.min_ngn_trx_amount'),
            'account_number' => 'sometimes|numeric',
            'account_name' => 'sometimes',
            'account_bank' => 'sometimes',
            'currency' => 'sometimes',
            'b2b' => 'sometimes',
            'type' => 'required'
        ];
    }
}
