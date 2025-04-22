<?php

namespace App\Http\Requests\Call;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('call'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'from' => ['sometimes', 'string', 'max:20'],
            'to' => ['sometimes', 'string', 'max:20'],
            'direction' => ['sometimes', 'string', Rule::in(['inbound', 'outbound'])],
            'status' => ['sometimes', 'string', Rule::in(['initiated', 'in-progress', 'completed', 'failed'])],
            'session_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'agent_id' => ['sometimes', 'nullable', 'uuid', 'exists:users,id'],
            'contact_id' => ['sometimes', 'nullable', 'uuid', 'exists:contacts,id'],
            'queue_id' => ['sometimes', 'nullable', 'uuid', 'exists:queues,id'],
            'department_id' => ['sometimes', 'nullable', 'uuid', 'exists:departments,id'],
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
            'from.string' => 'The from phone number must be a string.',
            'from.max' => 'The from phone number must not exceed 20 characters.',
            'to.string' => 'The to phone number must be a string.',
            'to.max' => 'The to phone number must not exceed 20 characters.',
            'direction.in' => 'The direction must be either inbound or outbound.',
            'status.in' => 'The status must be one of: initiated, in-progress, completed, failed.',
            'session_id.string' => 'The session ID must be a string.',
            'session_id.max' => 'The session ID must not exceed 255 characters.',
            'agent_id.exists' => 'The specified agent does not exist.',
            'contact_id.exists' => 'The specified contact does not exist.',
            'queue_id.exists' => 'The specified queue does not exist.',
            'department_id.exists' => 'The specified department does not exist.',
        ];
    }
}
