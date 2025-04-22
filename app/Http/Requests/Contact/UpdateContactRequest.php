<?php

namespace App\Http\Requests\Contact;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('contact'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
            'favorite_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'identifiers' => ['sometimes', 'array', 'min:1'],
            'identifiers.*.type' => ['required_with:identifiers', 'string', 'in:phone,email,external_id'],
            'identifiers.*.value' => [
                'required_with:identifiers',
                'string',
                'max:255',
                Rule::when(
                    fn($input) => $input['type'] === 'phone',
                    ['regex:/^\+[1-9]\d{1,14}$/'],
                    ['email:rfc,dns']
                )
            ],
            'identifiers.*.is_primary' => ['sometimes', 'boolean'],
            'custom_attributes' => ['sometimes', 'array'],
            'custom_attributes.*.custom_attribute_id' => [
                'required_with:custom_attributes',
                'uuid',
                Rule::exists('custom_attributes', 'id')->where('organization_id', $this->input('organization_id'))
            ],
            'custom_attributes.*.value' => ['required_with:custom_attributes'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => [
                'string',
                'max:100',
                Rule::exists('tags', 'name')->where('organization_id', $this->input('organization_id'))
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
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'favorite_user_id.exists' => 'The specified favorite user does not exist.',
            'identifiers.min' => 'At least one identifier is required.',
            'identifiers.*.type.required_with' => 'The identifier type is required.',
            'identifiers.*.type.in' => 'The identifier type must be phone, email, or external_id.',
            'identifiers.*.value.required_with' => 'The identifier value is required.',
            'identifiers.*.value.max' => 'The identifier value must not exceed 255 characters.',
            'identifiers.*.value.regex' => 'The phone number must be in E.164 format (e.g., +1234567890).',
            'identifiers.*.value.email' => 'The email must be a valid email address.',
            'custom_attributes.*.custom_attribute_id.required_with' => 'The custom attribute ID is required.',
            'custom_attributes.*.custom_attribute_id.exists' => 'The specified custom attribute does not exist in the organization.',
            'custom_attributes.*.value.required_with' => 'The custom attribute value is required.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag must not exceed 100 characters.',
            'tags.*.exists' => 'The specified tag does not exist in the organization.',
        ];
    }
}
