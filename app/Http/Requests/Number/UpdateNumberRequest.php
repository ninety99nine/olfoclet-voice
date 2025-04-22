<?php

namespace App\Http\Requests\Number;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNumberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('number'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'number' => ['sometimes', 'string', 'max:20', 'regex:/^\+[1-9]\d{1,14}$/', Rule::unique('numbers')->ignore($this->route('number')->id)],
            'provider' => ['sometimes', 'nullable', 'string', 'max:100'],
            'call_flow_id' => ['sometimes', 'nullable', 'uuid', 'exists:call_flows,id'],
            'is_active' => ['sometimes', 'boolean'],
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
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'number.string' => 'The phone number must be a string.',
            'number.max' => 'The phone number must not exceed 20 characters.',
            'number.regex' => 'The phone number must be in E.164 format (e.g., +1234567890).',
            'number.unique' => 'The phone number is already in use.',
            'provider.string' => 'The provider must be a string.',
            'provider.max' => 'The provider must not exceed 100 characters.',
            'call_flow_id.uuid' => 'The call flow ID must be a valid UUID.',
            'call_flow_id.exists' => 'The specified call flow does not exist.',
            'is_active.boolean' => 'The is_active field must be a boolean.',
        ];
    }
}
