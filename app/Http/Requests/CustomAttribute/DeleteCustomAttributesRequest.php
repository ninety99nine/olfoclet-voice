<?php

namespace App\Http\Requests\CustomAttribute;

use App\Models\CustomAttribute;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCustomAttributesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', CustomAttribute::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'custom_attribute_ids' => ['required', 'array', 'min:1'],
            'custom_attribute_ids.*' => ['uuid', 'exists:custom_attributes,id'],
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
            'custom_attribute_ids.required' => 'The custom attribute IDs are required.',
            'custom_attribute_ids.array' => 'The custom attribute IDs must be an array.',
            'custom_attribute_ids.min' => 'At least one custom attribute ID is required.',
            'custom_attribute_ids.*.uuid' => 'Each custom attribute ID must be a valid UUID.',
            'custom_attribute_ids.*.exists' => 'One or more custom attribute IDs do not exist.',
        ];
    }
}
