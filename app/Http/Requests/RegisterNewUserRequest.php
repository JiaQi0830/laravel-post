<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterNewUserRequest extends FormRequest
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
            'name'      => ['required', 'max:255'],
            'email'     => ['required', 'email', 'unique:users', 'max:255'],
            'password'  => ['required', 'min:6', 'confirmed']
        ];
    }

}
