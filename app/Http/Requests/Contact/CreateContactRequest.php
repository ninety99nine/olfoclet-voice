<?php

namespace App\Http\Requests\Contact;

use App\Models\Contact;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Contact::class);
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
            'identifiers' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $hasPhone = collect($value)->contains('type', 'phone');
                    if (!$hasPhone) {
                        $fail("At least one phone number is required for each contact.");
                    }
                },
            ],
            'identifiers.*.type' => ['required', 'string', 'in:phone,email,external_id'],
            'identifiers.*.value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $type = $this->input("identifiers.$index.type");
                    if ($type === 'phone' && !preg_match('/^\+[1-9]\d{1,14}$/', $value)) {
                        $fail("The $attribute '$value' must be a valid phone number in E.164 format (e.g., +1234567890).");
                    } elseif ($type === 'email') {
                        $emailValidator = Validator::make(['email' => $value], ['email' => 'email:rfc']);
                        if ($emailValidator->fails()) {
                            $fail("The $attribute '$value' must be a valid email address.");
                        }
                    }
                },
            ],
            'identifiers.*.is_primary' => ['sometimes', 'boolean'],
            'custom_attributes' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $hasName = collect($value)->contains('name', 'name');
                    if (!$hasName) {
                        $fail("The 'name' custom attribute is required for each contact.");
                    }
                    $nameValue = collect($value)->firstWhere('name', 'name')['value'] ?? '';
                    if (empty($nameValue)) {
                        $fail("The 'name' custom attribute must have a non-empty value.");
                    }
                },
            ],
            'custom_attributes.*.custom_attribute_id' => [
                'required',
                'uuid',
                Rule::exists('custom_attributes', 'id')->where('organization_id', $this->input('organization_id'))
            ],
            'custom_attributes.*.name' => ['required', 'string'],
            'custom_attributes.*.type' => ['required', 'string', 'in:string,url,number,date'],
            'custom_attributes.*.value' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $type = $this->input("custom_attributes.$index.type");
                    if ($type === 'url') {
                        $urlValidator = Validator::make(['url' => $value], ['url' => 'url']);
                        if ($urlValidator->fails()) {
                            $fail("The $attribute '$value' must be a valid URL (e.g., https://example.com).");
                        }
                    }
                },
            ],
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
            'identifiers.required' => 'At least one identifier is required.',
            'identifiers.min' => 'At least one identifier is required.',
            'identifiers.*.type.required' => 'The identifier type is required.',
            'identifiers.*.type.in' => 'The identifier type must be phone, email, or external_id.',
            'identifiers.*.value.required' => 'The identifier value is required.',
            'identifiers.*.value.max' => 'The identifier value must not exceed 255 characters.',
            'identifiers.*.value.regex' => 'The phone number must be in E.164 format (e.g., +1234567890).',
            'identifiers.*.value.email' => 'The email must be a valid email address.',
            'custom_attributes.*.custom_attribute_id.required' => 'The custom attribute ID is required.',
            'custom_attributes.*.custom_attribute_id.exists' => 'The specified custom attribute does not exist in the organization.',
            'custom_attributes.*.name.required' => 'The custom attribute name is required.',
            'custom_attributes.*.type.required' => 'The custom attribute type is required.',
            'custom_attributes.*.type.in' => 'The custom attribute type must be string, url, number, or date.',
            'custom_attributes.*.value.required' => 'The custom attribute value is required.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag must not exceed 100 characters.',
            'tags.*.exists' => 'The specified tag does not exist in the organization.',
        ];
    }
}
