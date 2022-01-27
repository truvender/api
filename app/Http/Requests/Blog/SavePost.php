<?php

namespace App\Http\Requests\Blog;

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
            'image' => 'nullable|image|mimes:png,jpg,gif,svg|max:10240',
            'body' => 'required|string',
        ];
    }
}
