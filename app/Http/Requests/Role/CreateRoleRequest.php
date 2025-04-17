<?php

namespace App\Http\Requests\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Role::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) {
                    // Only apply organization_id scoping if provided
                    return $query->when($this->input('organization_id'), fn($q) => $q->where('organization_id', $this->input('organization_id')));
                }),
            ],
            'organization_id' => ['nullable', 'uuid', 'exists:organizations,id'],
        ];
    }
}
