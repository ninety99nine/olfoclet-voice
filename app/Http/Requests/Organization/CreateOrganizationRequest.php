<?php

namespace App\Http\Requests\Organization;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Organization::class);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'country' => 'required|string',
            'active' => 'sometimes|boolean',
            'seats' => 'required|integer|min:1',
        ];
    }
}
