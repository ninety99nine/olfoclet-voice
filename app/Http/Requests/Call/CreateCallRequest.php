<?php

namespace App\Http\Requests\Call;

use App\Models\Call;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Call::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'from' => 'required|string|max:20',
            'to' => 'required|string|max:20',
            'direction' => ['required', 'string', Rule::in(['inbound', 'outbound'])],
            'status' => ['sometimes', 'string', Rule::in(['initiated', 'in-progress', 'completed', 'failed'])],
            'session_id' => 'sometimes|nullable|string|max:255',
            'organization_id' => 'required|uuid|exists:organizations,id',
            'agent_id' => 'sometimes|nullable|uuid|exists:users,id',
            'contact_id' => 'sometimes|nullable|uuid|exists:contacts,id',
            'queue_id' => 'sometimes|nullable|uuid|exists:queues,id',
            'department_id' => 'sometimes|nullable|uuid|exists:departments,id',
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
            'from.required' => 'The from phone number is required.',
            'from.max' => 'The from phone number must not exceed 20 characters.',
            'to.required' => 'The to phone number is required.',
            'to.max' => 'The to phone number must not exceed 20 characters.',
            'direction.required' => 'The call direction is required.',
            'direction.in' => 'The direction must be either inbound or outbound.',
            'status.in' => 'The status must be one of: initiated, in-progress, completed, failed.',
            'session_id.max' => 'The session ID must not exceed 255 characters.',
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.exists' => 'The specified organization does not exist.',
            'agent_id.exists' => 'The specified agent does not exist.',
            'contact_id.exists' => 'The specified contact does not exist.',
            'queue_id.exists' => 'The specified queue does not exist.',
            'department_id.exists' => 'The specified department does not exist.',
        ];
    }
}
