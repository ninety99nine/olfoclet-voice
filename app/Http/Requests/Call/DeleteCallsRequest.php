<?php

namespace App\Http\Requests\Call;

use App\Models\Call;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCallsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Call::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_ids' => ['required', 'array', 'min:1'],
            'call_ids.*' => ['uuid', 'exists:calls,id'],
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
            'call_ids.required' => 'The call IDs are required.',
            'call_ids.array' => 'The call IDs must be an array.',
            'call_ids.min' => 'At least one call ID is required.',
            'call_ids.*.uuid' => 'Each call ID must be a valid UUID.',
            'call_ids.*.exists' => 'One or more call IDs do not exist.',
        ];
    }
}
