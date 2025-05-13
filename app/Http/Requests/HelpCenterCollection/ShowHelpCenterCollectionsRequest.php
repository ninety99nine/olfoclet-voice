<?php

namespace App\Http\Requests\HelpCenterCollection;

use App\Models\HelpCenterCollection;
use Illuminate\Foundation\Http\FormRequest;

class ShowHelpCenterCollectionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', HelpCenterCollection::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['sometimes', 'uuid', 'exists:organizations,id'],
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
            'organization_id.uuid' => 'The organization ID must be a valid UUID.',
            'organization_id.exists' => 'The specified organization does not exist.',
        ];
    }
}
