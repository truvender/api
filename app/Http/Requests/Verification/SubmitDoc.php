<?php

namespace App\Http\Requests\Verification;

use App\Helpers\Constants;
use Illuminate\Foundation\Http\FormRequest;

class SubmitDoc extends FormRequest
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
            'name' => 'required|string',
            'date_of_birth' => 'required|string',
            'type' => 'required|string',
            'document' => Constants::REQUIRED_IMAGE_VALIDATION,
        ];
    }
}
