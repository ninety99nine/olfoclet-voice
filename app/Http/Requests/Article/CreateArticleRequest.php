<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Article::class);
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
            'content' => 'required|string',
            'ai_searchable' => 'sometimes|boolean',
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
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'content.required' => 'The content field is required.',
            'ai_searchable.boolean' => 'The ai_searchable field must be a boolean.',
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'knowledge_base_id.required' => 'The knowledge base ID is required.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
