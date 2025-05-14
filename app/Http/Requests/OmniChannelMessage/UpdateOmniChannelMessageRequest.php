<?php

namespace App\Http\Requests\OmniChannelMessage;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOmniChannelMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('omniChannelMessage'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'channel' => ['sometimes', 'string', Rule::in(['whatsapp', 'instagram', 'telegram', 'messenger', 'facebook', 'wechat', 'line', 'sms'])],
            'direction' => ['sometimes', 'string', Rule::in(['inbound', 'outbound'])],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'sent', 'delivered', 'read', 'failed'])],
            'from' => 'sometimes|string|max:50',
            'to' => 'sometimes|string|max:50',
            'content' => 'sometimes|string',
            'message_type' => 'sometimes|nullable|string|max:50',
            'external_message_id' => 'sometimes|nullable|string|max:255',
            'sent_at' => 'sometimes|nullable|date',
            'delivered_at' => 'sometimes|nullable|date',
            'read_at' => 'sometimes|nullable|date',
            'error_message' => 'sometimes|nullable|string',
            'metadata' => 'sometimes|nullable|array',
            'contact_id' => 'sometimes|nullable|uuid|exists:contacts,id',
            'agent_id' => 'sometimes|nullable|uuid|exists:users,id',
            'queue_id' => 'sometimes|nullable|uuid|exists:queues,id',
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
            'channel.in' => 'The channel must be one of: whatsapp, instagram, telegram, messenger, facebook, wechat, line, sms.',
            'direction.in' => 'The direction must be either inbound or outbound.',
            'status.in' => 'The status must be one of: pending, sent, delivered, read, failed.',
            'from.max' => 'The from field must not exceed 50 characters.',
            'to.max' => 'The to field must not exceed 50 characters.',
            'message_type.max' => 'The message type must not exceed 50 characters.',
            'external_message_id.max' => 'The external message ID must not exceed 255 characters.',
            'sent_at.date' => 'The sent at field must be a valid date.',
            'delivered_at.date' => 'The delivered at field must be a valid date.',
            'read_at.date' => 'The read at field must be a valid date.',
            'metadata.array' => 'The metadata must be an array.',
            'contact_id.exists' => 'The specified contact does not exist.',
            'agent_id.exists' => 'The specified agent does not exist.',
            'queue_id.exists' => 'The specified queue does not exist.',
        ];
    }
}
