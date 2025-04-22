<?php

namespace App\Http\Requests\Number;

use App\Models\Number;
use Illuminate\Foundation\Http\FormRequest;

class DeleteNumbersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Number::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'number_ids' => ['required', 'array', 'min:1'],
            'number_ids.*' => ['uuid', 'exists:numbers,id'],
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
            'number_ids.required' => 'The number IDs are required.',
            'number_ids.array' => 'The number IDs must be an array.',
            'number_ids.min' => 'At least one number ID is required.',
            'number_ids.*.uuid' => 'Each number ID must be a valid UUID.',
            'number_ids.*.exists' => 'One or more number IDs do not exist.',
        ];
    }
}
