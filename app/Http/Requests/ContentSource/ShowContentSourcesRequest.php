<?php

namespace App\Http\Requests\ContentSource;

use App\Models\ContentSource;
use Illuminate\Foundation\Http\FormRequest;

class ShowContentSourcesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', ContentSource::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'knowledge_base_id' => ['sometimes', 'uuid', 'exists:knowledge_bases,id'],
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
            'knowledge_base_id.uuid' => 'The knowledge base ID must be a valid UUID.',
            'knowledge_base_id.exists' => 'The specified knowledge base does not exist.',
        ];
    }
}
