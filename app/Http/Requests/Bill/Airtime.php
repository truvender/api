<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class Airtime extends FormRequest
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
            'customer' => 'required',
            'provider' => 'required',
            'amount' => 'required|numeric',
            'pay_from' => 'required'
        ];
    }
}
