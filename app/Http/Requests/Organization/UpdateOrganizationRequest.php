<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->organization);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'country' => 'required|string',
            'active' => 'sometimes|boolean',
            'seats' => 'sometimes|integer|min:1',
        ];
    }

}
