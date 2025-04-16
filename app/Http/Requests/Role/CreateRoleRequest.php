<?php

namespace App\Http\Requests\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Role::class);
    }

    public function rules()
    {
        return [
            'name' => [
                'required','string','max:255',
                Rule::unique('roles')->where(function ($query) {
                    return $query->where('organization_id', $this->input('organization_id'));
                })
            ],
            'organization_id' => [ 'required', 'uuid', 'exists:organizations,id' ]
        ];
    }
}
