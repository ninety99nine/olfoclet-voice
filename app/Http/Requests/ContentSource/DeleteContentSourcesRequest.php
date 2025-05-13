<?php

namespace App\Http\Requests\ContentSource;

use App\Models\ContentSource;
use Illuminate\Foundation\Http\FormRequest;

class DeleteContentSourcesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', ContentSource::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_source_ids' => ['required', 'array', 'min:1'],
            'content_source_ids.*' => ['uuid', 'exists:content_sources,id'],
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
            'content_source_ids.required' => 'The content source IDs are required.',
            'content_source_ids.array' => 'The content source IDs must be an array.',
            'content_source_ids.min' => 'At least one content source ID is required.',
            'content_source_ids.*.uuid' => 'Each content source ID must be a valid UUID.',
            'content_source_ids.*.exists' => 'One or more content source IDs do not exist.',
            'knowledge_base_id.uuid' => 'The knowledge base ID must be a valid UUID.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
