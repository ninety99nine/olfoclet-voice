<?php

namespace App\Http\Requests\ContentItem;

use App\Models\ContentItem;
use Illuminate\Foundation\Http\FormRequest;

class DeleteContentItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', ContentItem::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_item_ids' => ['required', 'array', 'min:1'],
            'content_item_ids.*' => ['uuid', 'exists:content_items,id'],
            'knowledge_base_id' => ['sometimes', 'uuid', 'exists:knowledge_bases,id'],
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
            'content_item_ids.required' => 'The content item IDs are required.',
            'content_item_ids.array' => 'The content item IDs must be an array.',
            'content_item_ids.min' => 'At least one content item ID is required.',
            'content_item_ids.*.uuid' => 'Each content item ID must be a valid UUID.',
            'content_item_ids.*.exists' => 'One or more content item IDs do not exist.',
            'knowledge_base_id.uuid' => 'The knowledge base ID must be a valid UUID.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
