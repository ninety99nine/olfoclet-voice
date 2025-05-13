<?php

namespace App\Http\Requests\HelpCenterCollection;

use App\Models\HelpCenterCollection;
use Illuminate\Foundation\Http\FormRequest;

class CreateHelpCenterCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', HelpCenterCollection::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'knowledge_base_id' => 'required|uuid|exists:knowledge_bases,id',
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
            'name.required' => 'The name field is required.',
            'name.max' => 'The name must not exceed 255 characters.',
            'knowledge_base_id.required' => 'The knowledge base ID is required.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
