<?php

namespace App\Http\Requests\ConversationThread;

use App\Models\ConversationThread;
use Illuminate\Foundation\Http\FormRequest;

class ShowConversationThreadsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ConversationThread::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'copilot_id' => ['sometimes', 'uuid', 'exists:copilots,id'],
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
            'copilot_id.uuid' => 'The copilot ID must be a valid UUID.',
            'copilot_id.exists' => 'The specified copilot does not exist.',
        ];
    }
}
