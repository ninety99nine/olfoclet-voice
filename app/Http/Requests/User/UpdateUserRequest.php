<?php

namespace App\Http\Requests\User;

use App\Enums\SystemRole;
use App\Models\User;
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
        return $this->user()->can('update', $this->route('user'));
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->route('user')->id)],
            'password' => [
                'sometimes',
                'nullable',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => ['required_with:password', 'string', 'nullable', 'same:password'],
            'type' => ['required', 'string', Rule::in(collect(SystemRole::cases())->pluck('value')->toArray())],
            'organization_id' => ["required_if:type,{$systemRole}", 'nullable', 'uuid', 'exists:organizations,id'],
            'active' => ['sometimes', 'boolean']
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
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'The email address is already in use.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.mixed_case' => 'The password must contain both uppercase and lowercase letters.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one special character.',
            'password_confirmation.required_with' => 'The password confirmation field is required when a new password is provided.',
            'password_confirmation.same' => 'The password confirmation does not match.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be a valid system role.',
            'organization_id.required_if' => 'The organization ID is required for regular users.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'active.boolean' => 'The active field must be a boolean.',
        ];
    }
}
