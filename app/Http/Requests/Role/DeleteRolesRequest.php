<?php

namespace App\Http\Requests\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class DeleteRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Role::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['uuid', 'exists:roles,id'],
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
            'role_ids.required' => 'The role IDs are required.',
            'role_ids.array' => 'The role IDs must be an array.',
            'role_ids.min' => 'At least one role ID is required.',
            'role_ids.*.uuid' => 'Each role ID must be a valid UUID.',
            'role_ids.*.exists' => 'One or more role IDs do not exist.',
        ];
    }
}
