<?php

namespace App\Http\Requests\OmniChannelMessage;

use App\Models\OmniChannelMessage;
use Illuminate\Foundation\Http\FormRequest;

class DeleteOmniChannelMessagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', OmniChannelMessage::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'omni_channel_message_ids' => ['required', 'array', 'min:1'],
            'omni_channel_message_ids.*' => ['uuid', 'exists:omni_channel_messages,id'],
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
            'omni_channel_message_ids.required' => 'The omni channel message IDs are required.',
            'omni_channel_message_ids.array' => 'The omni channel message IDs must be an array.',
            'omni_channel_message_ids.min' => 'At least one omni channel message ID is required.',
            'omni_channel_message_ids.*.uuid' => 'Each omni channel message ID must be a valid UUID.',
            'omni_channel_message_ids.*.exists' => 'One or more omni channel message IDs do not exist.',
        ];
    }
}
