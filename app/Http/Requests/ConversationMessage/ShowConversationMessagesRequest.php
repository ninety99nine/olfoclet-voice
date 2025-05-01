<?php

namespace App\Http\Requests\ConversationMessage;

use App\Models\ConversationMessage;
use Illuminate\Foundation\Http\FormRequest;

class ShowConversationMessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ConversationMessage::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'thread_id' => ['sometimes', 'uuid', 'exists:conversation_threads,id'],
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
            'thread_id.uuid' => 'The thread ID must be a valid UUID.',
            'thread_id.exists' => 'The specified thread does not exist.',
        ];
    }
}
