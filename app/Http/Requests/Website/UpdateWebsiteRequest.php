<?php

namespace App\Http\Requests\Website;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('website'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'url' => ['sometimes', 'url', 'max:255'],
            'ai_searchable' => ['sometimes', 'boolean'],
            'sync_status' => ['sometimes', 'string', Rule::in(['pending', 'syncing', 'completed', 'failed'])],
            'last_synced_at' => ['sometimes', 'nullable', 'date'],
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
            'url.url' => 'The URL must be a valid URL.',
            'url.max' => 'The URL must not exceed 255 characters.',
            'ai_searchable.boolean' => 'The ai_searchable field must be a boolean.',
            'sync_status.in' => 'The sync status must be one of: pending, syncing, completed, failed.',
            'last_synced_at.date' => 'The last synced at must be a valid date.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
