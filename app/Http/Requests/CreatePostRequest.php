<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public $validator = null;

    public function rules()
    {
        return [
            'title' => ['required'],
            'content' => ['required']
        ];
    }
}
