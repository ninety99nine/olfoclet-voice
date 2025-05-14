<?php

namespace App\Http\Requests\Interaction;

use App\Models\Interaction;
use Illuminate\Foundation\Http\FormRequest;

class DeleteInteractionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Interaction::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'interaction_ids' => ['required', 'array', 'min:1'],
            'interaction_ids.*' => ['uuid', 'exists:interactions,id'],
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
            'interaction_ids.required' => 'The interaction IDs are required.',
            'interaction_ids.array' => 'The interaction IDs must be an array.',
            'interaction_ids.min' => 'At least one interaction ID is required.',
            'interaction_ids.*.uuid' => 'Each interaction ID must be a valid UUID.',
            'interaction_ids.*.exists' => 'One or more interaction IDs do not exist.',
        ];
    }
}
