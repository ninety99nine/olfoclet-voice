<?php

namespace App\Http\Requests\Organization;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('organization'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('organizations')->ignore($this->route('organization')->id)],
            'country' => 'required|string',
            'active' => 'sometimes|boolean',
            'seats' => 'sometimes|integer|min:1',
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
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'name.unique' => 'The name is already in use.',
            'country.required' => 'The country is required.',
            'country.string' => 'The country must be a string.',
            'active.boolean' => 'The active field must be a boolean.',
            'seats.integer' => 'The seats must be an integer.',
            'seats.min' => 'The seats must be at least 1.',
        ];
    }
}
