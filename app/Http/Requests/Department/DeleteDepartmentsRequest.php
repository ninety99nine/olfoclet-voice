<?php

namespace App\Http\Requests\Department;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class DeleteDepartmentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Department::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'department_ids' => ['required', 'array', 'min:1'],
            'department_ids.*' => ['uuid', 'exists:departments,id'],
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
            'department_ids.required' => 'The department IDs are required.',
            'department_ids.array' => 'The department IDs must be an array.',
            'department_ids.min' => 'At least one department ID is required.',
            'department_ids.*.uuid' => 'Each department ID must be a valid UUID.',
            'department_ids.*.exists' => 'One or more department IDs do not exist.',
        ];
    }
}
