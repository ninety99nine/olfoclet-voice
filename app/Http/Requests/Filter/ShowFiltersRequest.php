<?php

namespace App\Http\Requests\Filter;

use Illuminate\Validation\Rule;
use App\Enums\FilterResourceType;
use Illuminate\Foundation\Http\FormRequest;

class ShowFiltersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Public endpoint for now; consider restricting to authenticated users in the future
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(collect(FilterResourceType::cases())->pluck('value')->toArray()),
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
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'type.in' => 'The type must be a valid resource type.',
        ];
    }
}
