<?php

namespace App\Http\Requests\ContentSource;

use App\Models\ContentSource;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateContentSourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', ContentSource::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['telcoflo', 'zendesk', 'guru', 'notion', 'confluence', 'website'])],
            'name' => 'required|string|max:255',
            'last_synced_at' => 'sometimes|nullable|date',
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
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: telcoflo, zendesk, guru, notion, confluence, website.',
            'name.required' => 'The name field is required.',
            'name.max' => 'The name must not exceed 255 characters.',
            'last_synced_at.date' => 'The last synced at must be a valid date.',
            'knowledge_base_id.required' => 'The knowledge base ID is required.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
