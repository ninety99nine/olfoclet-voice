<?php

namespace App\Http\Requests\User;

use App\Enums\SystemRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user);
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
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => [
                'sometimes',
                'nullable',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => 'required_with:password|string|nullable',
            'type' => ['required', 'string', Rule::in(collect(SystemRole::cases())->pluck('value')->toArray())],
            'organization_id' => "required_if:type,{$systemRole}|nullable|uuid|exists:organizations,id",
            'active' => 'sometimes|boolean',
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
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.mixed_case' => 'The password must contain both uppercase and lowercase letters.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one special character.',
            'password_confirmation.required_with' => 'The password confirmation field is required when a new password is provided.',
            'type.required' => 'The type field is required.',
            'organization_id.required_if' => 'The organization ID is required for regular users.',
            'organization_id.exists' => 'The specified organization does not exist.',
        ];
    }
}
