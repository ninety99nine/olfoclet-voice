<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->user);
    }

    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'type'     => 'required|string',
            'active'   => 'sometimes|boolean'
        ];
    }
}
