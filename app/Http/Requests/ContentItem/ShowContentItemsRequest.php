<?php

namespace App\Http\Requests\ContentItem;

use App\Models\ContentItem;
use Illuminate\Foundation\Http\FormRequest;

class ShowContentItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ContentItem::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'knowledge_base_id' => ['sometimes', 'uuid', 'exists:knowledge_bases,id'],
            'parent_id' => ['sometimes', 'uuid', 'exists:content_items,id'],
            'type' => ['sometimes', 'in:folder,article,snippet,webpage'],
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
            'knowledge_base_id.uuid' => 'The knowledge base ID must be a valid UUID.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
            'parent_id.uuid' => 'The parent ID must be a valid UUID.',
            'parent_id.exists' => 'The specified parent does not exist.',
            'type.in' => 'The type must be one of: folder, article, snippet, webpage.',
        ];
    }
}
