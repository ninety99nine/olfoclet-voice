<?php

namespace App\Http\Requests\CallFlowNode;

use App\Models\CallFlowNode;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCallFlowNodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CallFlowNode::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_flow_id' => ['required', 'uuid', 'exists:call_flows,id'],
            'type' => ['required', 'string', Rule::in(['Playback', 'IVR', 'Forward', 'Voicemail', 'Hangup'])],
            'next_step' => ['sometimes', 'nullable', 'string', 'max:36'],
            'metadata' => ['required', 'array'],
            'position' => ['required', 'array'],
            'position.x' => ['required', 'numeric'],
            'position.y' => ['required', 'numeric'],
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
            'call_flow_id.required' => 'The call flow ID is required.',
            'call_flow_id.uuid' => 'The call flow ID must be a valid UUID.',
            'call_flow_id.exists' => 'The specified call flow does not exist.',
            'type.required' => 'The node type is required.',
            'type.in' => 'The node type must be one of: Playback, IVR, Forward, Voicemail, Hangup.',
            'next_step.string' => 'The next step must be a string.',
            'next_step.max' => 'The next step must not exceed 36 characters.',
            'metadata.required' => 'The metadata is required.',
            'metadata.array' => 'The metadata must be a valid JSON object.',
            'position.required' => 'The position is required.',
            'position.array' => 'The position must be a valid JSON object.',
            'position.x.required' => 'The position x-coordinate is required.',
            'position.x.numeric' => 'The position x-coordinate must be numeric.',
            'position.y.required' => 'The position y-coordinate is required.',
            'position.y.numeric' => 'The position y-coordinate must be numeric.',
        ];
    }
}
