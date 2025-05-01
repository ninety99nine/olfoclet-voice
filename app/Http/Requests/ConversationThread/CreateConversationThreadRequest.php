<?php

namespace App\Http\Requests\ConversationThread;

use App\Models\ConversationThread;
use Illuminate\Foundation\Http\FormRequest;

class CreateConversationThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', ConversationThread::class);
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
            'copilot_id' => 'required|uuid|exists:copilots,id',
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
            'copilot_id.required' => 'The copilot ID is required.',
            'copilot_id.exists' => 'The specified copilot does not exist.',
        ];
    }
}
