<?php

namespace App\Http\Requests\Blog;

use App\Helpers\Constants;
use Illuminate\Foundation\Http\FormRequest;

class SavePost extends FormRequest
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
            'category' => 'nullable|uuid',
            'title' => 'required|string',
            'image' => Constants::IMAGE_VALIDATION,
            'body' => 'required|string',
        ];
    }
}
