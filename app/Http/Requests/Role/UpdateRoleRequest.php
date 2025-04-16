<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->role);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255'
        ];
    }
}
