<?php

namespace App\Http\Requests\ConversationMessage;

use App\Models\ConversationMessage;
use Illuminate\Foundation\Http\FormRequest;

class DeleteConversationMessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', ConversationMessage::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message_ids' => ['required', 'array', 'min:1'],
            'message_ids.*' => ['uuid', 'exists:conversation_messages,id'],
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
            'message_ids.required' => 'The message IDs are required.',
            'message_ids.array' => 'The message IDs must be an array.',
            'message_ids.min' => 'At least one message ID is required.',
            'message_ids.*.uuid' => 'Each message ID must be a valid UUID.',
            'message_ids.*.exists' => 'One or more message IDs do not exist.',
            'thread_id.uuid' => 'The thread ID must be a valid UUID.',
            'thread_id.exists' => 'The specified thread does not exist.',
        ];
    }
}
