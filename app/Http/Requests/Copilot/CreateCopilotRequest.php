<?php

namespace App\Http\Requests\Copilot;

use App\Models\Copilot;
use Illuminate\Foundation\Http\FormRequest;

class CreateCopilotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Copilot::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'organization_id' => 'required|uuid|exists:organizations,id',
            'knowledge_base_ids' => 'sometimes|array',
            'knowledge_base_ids.*' => 'uuid|exists:knowledge_bases,id',
            'user_ids' => 'sometimes|array',
            'user_ids.*' => 'uuid|exists:users,id',
            'is_active' => 'sometimes|boolean',
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
            'description.string' => 'The description must be a string.',
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.uuid' => 'The organization ID must be a valid UUID.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'knowledge_base_ids.array' => 'The knowledge base IDs must be an array.',
            'knowledge_base_ids.*.uuid' => 'Each knowledge base ID must be a valid UUID.',
            'knowledge_base_ids.*.exists' => 'One or more knowledge base IDs do not exist.',
            'user_ids.array' => 'The user IDs must be an array.',
            'user_ids.*.uuid' => 'Each user ID must be a valid UUID.',
            'user_ids.*.exists' => 'One or more user IDs do not exist.',
            'is_active.boolean' => 'The is_active field must be a boolean.',
        ];
    }
}
