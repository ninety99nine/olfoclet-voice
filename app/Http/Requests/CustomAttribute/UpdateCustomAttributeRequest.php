<?php

namespace App\Http\Requests\CustomAttribute;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('customAttribute'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('custom_attributes')
                    ->where('organization_id', $this->route('customAttribute')->organization_id)
                    ->ignore($this->route('customAttribute')->id)
            ],
            'type' => ['sometimes', 'string', 'in:text,textarea,url'],
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
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'name.unique' => 'The name is already in use for this organization.',
            'type.in' => 'The type must be one of: text, textarea, url.',
        ];
    }
}
