<?php

namespace App\Http\Requests\CallFlowNode;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCallFlowNodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('callFlowNode'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', Rule::in(['Playback', 'IVR', 'Forward', 'Voicemail', 'Hangup'])],
            'next_step' => ['sometimes', 'nullable', 'string', 'max:36'],
            'metadata' => ['sometimes', 'array'],
            'position' => ['sometimes', 'array'],
            'position.x' => ['sometimes', 'numeric'],
            'position.y' => ['sometimes', 'numeric'],
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
            'type.in' => 'The node type must be one of: Playback, IVR, Forward, Voicemail, Hangup.',
            'next_step.string' => 'The next step must be a string.',
            'next_step.max' => 'The next step must not exceed 36 characters.',
            'metadata.array' => 'The metadata must be a valid JSON object.',
            'position.array' => 'The position must be a valid JSON object.',
            'position.x.numeric' => 'The position x-coordinate must be numeric.',
            'position.y.numeric' => 'The position y-coordinate must be numeric.',
        ];
    }
}
