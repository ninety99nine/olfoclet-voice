<?php

namespace App\Http\Requests\Copilot;

use Illuminate\Foundation\Http\FormRequest;

class QueryCopilotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('copilot'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|max:1000',
            'conversation_thread_id' => 'nullable|uuid|exists:conversation_threads,id',
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
            'query.required' => 'The query field is required.',
            'query.string' => 'The query must be a string.',
            'query.max' => 'The query must not exceed 1000 characters.',
            'conversation_thread_id.uuid' => 'The conversation thread ID must be a valid UUID.',
            'conversation_thread_id.exists' => 'The specified conversation thread does not exist.',
        ];
    }
}
