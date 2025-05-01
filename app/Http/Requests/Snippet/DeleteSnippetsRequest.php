<?php

namespace App\Http\Requests\Snippet;

use App\Models\Snippet;
use Illuminate\Foundation\Http\FormRequest;

class DeleteSnippetsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Snippet::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'snippet_ids' => ['required', 'array', 'min:1'],
            'snippet_ids.*' => ['uuid', 'exists:snippets,id'],
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
            'snippet_ids.required' => 'The snippet IDs are required.',
            'snippet_ids.array' => 'The snippet IDs must be an array.',
            'snippet_ids.min' => 'At least one snippet ID is required.',
            'snippet_ids.*.uuid' => 'Each snippet ID must be a valid UUID.',
            'snippet_ids.*.exists' => 'One or more snippet IDs do not exist.',
        ];
    }
}
