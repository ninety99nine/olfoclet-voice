<?php

namespace App\Http\Requests\ConversationThread;

use App\Models\ConversationThread;
use Illuminate\Foundation\Http\FormRequest;

class DeleteConversationThreadsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', ConversationThread::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'thread_ids' => ['required', 'array', 'min:1'],
            'thread_ids.*' => ['uuid', 'exists:conversation_threads,id'],
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
            'thread_ids.required' => 'The thread IDs are required.',
            'thread_ids.array' => 'The thread IDs must be an array.',
            'thread_ids.min' => 'At least one thread ID is required.',
            'thread_ids.*.uuid' => 'Each thread ID must be a valid UUID.',
            'thread_ids.*.exists' => 'One or more thread IDs do not exist.',
        ];
    }
}
