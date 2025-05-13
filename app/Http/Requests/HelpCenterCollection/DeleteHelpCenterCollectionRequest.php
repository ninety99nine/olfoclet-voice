<?php

namespace App\Http\Requests\HelpCenterCollection;

use App\Models\HelpCenterCollection;
use Illuminate\Foundation\Http\FormRequest;

class DeleteHelpCenterCollectionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', HelpCenterCollection::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'help_center_collection_ids' => ['required', 'array', 'min:1'],
            'help_center_collection_ids.*' => ['uuid', 'exists:help_center_collections,id'],
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
            'help_center_collection_ids.required' => 'The help center collection IDs are required.',
            'help_center_collection_ids.array' => 'The help center collection IDs must be an array.',
            'help_center_collection_ids.min' => 'At least one help center collection ID is required.',
            'help_center_collection_ids.*.uuid' => 'Each help center collection ID must be a valid UUID.',
            'help_center_collection_ids.*.exists' => 'One or more help center collection IDs do not exist.',
        ];
    }
}
