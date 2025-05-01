<?php

namespace App\Http\Requests\Copilot;

use App\Models\Copilot;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCopilotsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Copilot::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'copilot_ids' => ['required', 'array', 'min:1'],
            'copilot_ids.*' => ['uuid', 'exists:copilots,id'],
            'organization_id' => ['sometimes', 'uuid', 'exists:organizations,id'],
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
            'copilot_ids.required' => 'The copilot IDs are required.',
            'copilot_ids.array' => 'The copilot IDs must be an array.',
            'copilot_ids.min' => 'At least one copilot ID is required.',
            'copilot_ids.*.uuid' => 'Each copilot ID must be a valid UUID.',
            'copilot_ids.*.exists' => 'One or more copilot IDs do not exist.',
            'organization_id.uuid' => 'The organization ID must be a valid UUID.',
            'organization_id.exists' => 'The specified organization does not exist.',
        ];
    }
}
