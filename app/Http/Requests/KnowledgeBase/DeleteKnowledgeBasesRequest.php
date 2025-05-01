<?php

namespace App\Http\Requests\KnowledgeBase;

use App\Models\KnowledgeBase;
use Illuminate\Foundation\Http\FormRequest;

class DeleteKnowledgeBasesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', KnowledgeBase::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'knowledge_base_ids' => ['required', 'array', 'min:1'],
            'knowledge_base_ids.*' => ['uuid', 'exists:knowledge_bases,id'],
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
            'knowledge_base_ids.required' => 'The knowledge base IDs are required.',
            'knowledge_base_ids.array' => 'The knowledge base IDs must be an array.',
            'knowledge_base_ids.min' => 'At least one knowledge base ID is required.',
            'knowledge_base_ids.*.uuid' => 'Each knowledge base ID must be a valid UUID.',
            'knowledge_base_ids.*.exists' => 'One or more knowledge base IDs do not exist.',
        ];
    }
}
