<?php

namespace App\Http\Requests\Queue;

use App\Models\Queue;
use Illuminate\Foundation\Http\FormRequest;

class DeleteQueuesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Queue::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'queue_ids' => ['required', 'array', 'min:1'],
            'queue_ids.*' => ['uuid', 'exists:queues,id'],
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
            'queue_ids.required' => 'The queue IDs are required.',
            'queue_ids.array' => 'The queue IDs must be an array.',
            'queue_ids.min' => 'At least one queue ID is required.',
            'queue_ids.*.uuid' => 'Each queue ID must be a valid UUID.',
            'queue_ids.*.exists' => 'One or more queue IDs do not exist.',
        ];
    }
}
