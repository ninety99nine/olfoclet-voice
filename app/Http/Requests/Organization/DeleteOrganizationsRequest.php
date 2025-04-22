<?php

namespace App\Http\Requests\Organization;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

class DeleteOrganizationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Organization::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'organization_ids' => ['required', 'array', 'min:1'],
            'organization_ids.*' => ['uuid', 'exists:organizations,id'],
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
            'organization_ids.required' => 'The organization IDs are required.',
            'organization_ids.array' => 'The organization IDs must be an array.',
            'organization_ids.min' => 'At least one organization ID is required.',
            'organization_ids.*.uuid' => 'Each organization ID must be a valid UUID.',
            'organization_ids.*.exists' => 'One or more organization IDs do not exist.',
        ];
    }
}
