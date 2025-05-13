<?php

namespace App\Http\Requests\ContentItem;

use App\Models\ContentItem;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateContentItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', ContentItem::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'sometimes|nullable|string',
            'locale' => 'sometimes|nullable|string|max:10',
            'ai_ingested' => 'sometimes|boolean',
            'copilot_enabled' => 'sometimes|boolean',
            'ai_agent_enabled' => 'sometimes|boolean',
            'help_center_enabled' => 'sometimes|boolean',
            'visibility' => [
                'nullable',
                'string',
                Rule::in(['public', 'internal']),
                Rule::requiredIf(fn() => $this->input('type') === 'article') // Required only for articles
            ],
            'state' => [
                'nullable',
                'string',
                Rule::in(['draft', 'active', 'archived']),
                Rule::requiredIf(fn() => $this->input('type') === 'article') // Required only for articles
            ],
            'type' => ['required', 'string', Rule::in(['folder', 'article', 'snippet', 'webpage'])],
            'parent_id' => 'sometimes|nullable|uuid|exists:content_items,id',
            'source_id' => 'sometimes|nullable|uuid|exists:content_sources,id',
            'help_center_collection_id' => 'sometimes|nullable|uuid|exists:help_center_collections,id',
            'knowledge_base_id' => 'required|uuid|exists:knowledge_bases,id',
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
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'content.string' => 'The content must be a string.',
            'locale.string' => 'The locale must be a string.',
            'locale.max' => 'The locale must not exceed 10 characters.',
            'ai_ingested.boolean' => 'The AI ingested field must be a boolean.',
            'copilot_enabled.boolean' => 'The Copilot enabled field must be a boolean.',
            'ai_agent_enabled.boolean' => 'The AI agent enabled field must be a boolean.',
            'help_center_enabled.boolean' => 'The Help Center enabled field must be a boolean.',
            'visibility.required' => 'The visibility field is required for articles.',
            'visibility.in' => 'The visibility must be either public or internal.',
            'state.required' => 'The state field is required for articles.',
            'state.in' => 'The state must be either draft, active or archived.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: folder, article, snippet, webpage.',
            'parent_id.uuid' => 'The parent ID must be a valid UUID.',
            'parent_id.exists' => 'The specified parent does not exist.',
            'source_id.uuid' => 'The source ID must be a valid UUID.',
            'source_id.exists' => 'The specified source does not exist.',
            'help_center_collection_id.uuid' => 'The Help Center collection ID must be a valid UUID.',
            'help_center_collection_id.exists' => 'The specified Help Center collection does not exist.',
            'knowledge_base_id.required' => 'The knowledge base ID is required.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
