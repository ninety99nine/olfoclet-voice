<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $systemRole = SystemRole::REGULAR->value;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'type' => ['required', 'string', Rule::in(collect(SystemRole::cases())->pluck('value')->toArray())],
            'organization_id' => "required_if:type,{$systemRole}|nullable|uuid|exists:organizations,id",
            'role_id' => [
                "required_if:type,{$systemRole}", // Required for regular users
                'nullable',
                'uuid',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('organization_id', $this->input('organization_id'));
                }),
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'The email address is already in use.',
            'type.required' => 'The type field is required.',
            'organization_id.required_if' => 'The organization ID is required for regular users.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'role_id.required_if' => 'The role is required for regular users.',
            'role_id.exists' => 'The specified role does not exist or does not belong to the selected organization.',
        ];
    }
}
