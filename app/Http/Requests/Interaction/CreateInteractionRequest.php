<?php

namespace App\Http\Requests\Interaction;

use App\Models\Interaction;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateInteractionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Interaction::class);
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
            'organization_id' => 'required|uuid|exists:organizations,id',
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
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.exists' => 'The specified organization does not exist.',
        ];
    }
}
