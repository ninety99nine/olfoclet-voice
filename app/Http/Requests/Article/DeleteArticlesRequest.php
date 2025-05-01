<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class DeleteArticlesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Article::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'article_ids' => ['required', 'array', 'min:1'],
            'article_ids.*' => ['uuid', 'exists:articles,id'],
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
            'article_ids.required' => 'The article IDs are required.',
            'article_ids.array' => 'The article IDs must be an array.',
            'article_ids.min' => 'At least one article ID is required.',
            'article_ids.*.uuid' => 'Each article ID must be a valid UUID.',
            'article_ids.*.exists' => 'One or more article IDs do not exist.',
        ];
    }
}
