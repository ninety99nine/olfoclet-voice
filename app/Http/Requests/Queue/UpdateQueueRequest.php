<?php

namespace App\Http\Requests\Queue;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQueueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('queue'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
            'department_id' => ['sometimes', 'nullable', 'uuid', 'exists:departments,id'],
            'sla_threshold' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'max_wait_time' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'escalation_threshold' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'avg_wait_time' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'service_level' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'abandonment_rate' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'last_sla_review' => ['sometimes', 'nullable', 'date'],
            'call_volume_warning_threshold' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'call_volume_critical_threshold' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'record_calls' => ['sometimes', 'boolean'],
            'strategy' => ['sometimes', 'string', Rule::in(['round robin', 'ring all', 'least calls', 'longest idle', 'random'])],
            'priority_level' => ['sometimes', 'string', Rule::in(['normal', 'vip'])],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'hold_music_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'greeting_message' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'wait_message' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'callback_enabled' => ['sometimes', 'boolean'],
            'fallback_queue_id' => ['sometimes', 'nullable', 'uuid', 'exists:queues,id'],
            'fallback_department_id' => ['sometimes', 'nullable', 'uuid', 'exists:departments,id'],
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
            'name.max' => 'The name must not exceed 100 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 255 characters.',
            'active.boolean' => 'The active field must be a boolean.',
            'department_id.exists' => 'The specified department does not exist.',
            'sla_threshold.integer' => 'The SLA threshold must be an integer.',
            'sla_threshold.min' => 'The SLA threshold must be at least 0.',
            'max_wait_time.integer' => 'The max wait time must be an integer.',
            'max_wait_time.min' => 'The max wait time must be at least 0.',
            'escalation_threshold.integer' => 'The escalation threshold must be an integer.',
            'escalation_threshold.min' => 'The escalation threshold must be at least 0.',
            'avg_wait_time.integer' => 'The average wait time must be an integer.',
            'avg_wait_time.min' => 'The average wait time must be at least 0.',
            'service_level.numeric' => 'The service level must be a number.',
            'service_level.min' => 'The service level must be at least 0.',
            'service_level.max' => 'The service level must not exceed 100.',
            'abandonment_rate.numeric' => 'The abandonment rate must be a number.',
            'abandonment_rate.min' => 'The abandonment rate must be at least 0.',
            'abandonment_rate.max' => 'The abandonment rate must not exceed 100.',
            'last_sla_review.date' => 'The last SLA review must be a valid date.',
            'call_volume_warning_threshold.integer' => 'The call volume warning threshold must be an integer.',
            'call_volume_warning_threshold.min' => 'The call volume warning threshold must be at least 0.',
            'call_volume_critical_threshold.integer' => 'The call volume critical threshold must be an integer.',
            'call_volume_critical_threshold.min' => 'The call volume critical threshold must be at least 0.',
            'record_calls.boolean' => 'The record calls field must be a boolean.',
            'strategy.in' => 'The strategy must be one of: round robin, ring all, least calls, longest idle, random.',
            'priority_level.in' => 'The priority level must be either normal or vip.',
            'metadata.array' => 'The metadata must be an array.',
            'hold_music_url.string' => 'The hold music URL must be a string.',
            'hold_music_url.max' => 'The hold music URL must not exceed 255 characters.',
            'greeting_message.string' => 'The greeting message must be a string.',
            'greeting_message.max' => 'The greeting message must not exceed 1000 characters.',
            'wait_message.string' => 'The wait message must be a string.',
            'wait_message.max' => 'The wait message must not exceed 1000 characters.',
            'callback_enabled.boolean' => 'The callback enabled field must be a boolean.',
            'fallback_queue_id.exists' => 'The specified fallback queue does not exist.',
            'fallback_department_id.exists' => 'The specified fallback department does not exist.',
        ];
    }
}
