<?php

namespace App\Http\Requests\Website;

use App\Models\Website;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateWebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Website::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'url' => 'required|url|max:255',
            'ai_searchable' => 'sometimes|boolean',
            'sync_status' => ['sometimes', 'string', Rule::in(['pending', 'syncing', 'completed', 'failed'])],
            'last_synced_at' => 'sometimes|nullable|date',
            'organization_id' => 'required|uuid|exists:organizations,id',
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
            'url.required' => 'The URL field is required.',
            'url.url' => 'The URL must be a valid URL.',
            'url.max' => 'The URL must not exceed 255 characters.',
            'ai_searchable.boolean' => 'The ai_searchable field must be a boolean.',
            'sync_status.in' => 'The sync status must be one of: pending, syncing, completed, failed.',
            'last_synced_at.date' => 'The last synced at must be a valid date.',
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'knowledge_base_id.required' => 'The knowledge base ID is required.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
