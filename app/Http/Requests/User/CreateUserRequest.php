<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', User::class);
    }

    public function rules()
    {
        $regularType = UserType::REGULAR->value;

        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6',
            'type'            => 'required|string',
            'organization_id' => "required_if:type,{$regularType}|nullable|exists:organizations,id",
            'active'          => 'sometimes|boolean',
        ];
    }

}
