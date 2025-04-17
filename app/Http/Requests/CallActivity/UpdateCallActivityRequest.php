<?php

namespace App\Http\Requests\CallActivity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCallActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->callActivity);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'activity_type' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
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
            'activity_type.max' => 'The activity type must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'performed_by.exists' => 'The specified user does not exist.',
            'metadata.array' => 'The metadata must be an array.',
        ];
    }
}
