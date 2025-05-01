<?php

namespace App\Http\Requests\Snippet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSnippetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('snippet'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'ai_searchable' => ['sometimes', 'boolean'],
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
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title must not exceed 255 characters.',
            'content.string' => 'The content must be a string.',
            'ai_searchable.boolean' => 'The ai_searchable field must be a boolean.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
