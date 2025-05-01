<?php

namespace App\Http\Requests\ConversationMessage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConversationMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('conversationMessage'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'role' => ['sometimes', 'string', Rule::in(['user', 'assistant', 'error'])],
            'content' => 'sometimes|string',
            'context' => 'sometimes|nullable|array',
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
            'role.string' => 'The role must be a string.',
            'role.in' => 'The role must be one of: user, assistant, error.',
            'content.string' => 'The content must be a string.',
            'context.array' => 'The context must be an array.',
        ];
    }
}
