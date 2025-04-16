<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', User::class);
    }

    public function rules()
    {
        $systemRole = SystemRole::REGULAR->value;

        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
            'type'            => 'required|string',
            'organization_id' => "required_if:type,{$systemRole}|nullable|exists:organizations,id",
            'active'          => 'sometimes|boolean',
        ];
    }

}
