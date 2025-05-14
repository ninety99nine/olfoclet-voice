<?php

namespace App\Http\Requests\Interaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInteractionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('interaction'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_id' => 'sometimes|nullable|uuid|exists:calls,id',
            'omni_channel_message_id' => 'sometimes|nullable|uuid|exists:omni_channel_messages,id',
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
            'call_id.exists' => 'The specified call does not exist.',
            'omni_channel_message_id.exists' => 'The specified omni channel message does not exist.',
        ];
    }
}
