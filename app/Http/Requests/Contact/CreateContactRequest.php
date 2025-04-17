<?php

namespace App\Http\Requests\CallActivity;

use App\Models\CallActivity;
use Illuminate\Foundation\Http\FormRequest;

class CreateCallActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', CallActivity::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_id' => 'required|uuid|exists:calls,id',
            'activity_type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'performed_by' => 'sometimes|nullable|uuid|exists:users,id',
            'metadata' => 'sometimes|nullable|array',
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
            'call_id.required' => 'The call ID is required.',
            'call_id.exists' => 'The specified call does not exist.',
            'activity_type.required' => 'The activity type is required.',
            'activity_type.max' => 'The activity type must not exceed 255 characters.',
            'description.required' => 'The description is required.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'performed_by.exists' => 'The specified user does not exist.',
            'metadata.array' => 'The metadata must be an array.',
        ];
    }
}
