<?php

namespace App\Http\Requests\CustomAttribute;

use App\Models\CustomAttribute;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCustomAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CustomAttribute::class);
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('custom_attributes')->where('organization_id', $this->input('organization_id'))
            ],
            'type' => ['required', 'string', 'in:text,textarea,url'],
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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'name.unique' => 'The name is already in use for this organization.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: text, textarea, url.',
        ];
    }
}
