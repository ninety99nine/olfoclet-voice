<?php

namespace App\Http\Requests\ContentSource;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContentSourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('contentSource'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', Rule::in(['telcoflo', 'zendesk', 'guru', 'notion', 'confluence', 'website'])],
            'name' => ['sometimes', 'string', 'max:255'],
            'last_synced_at' => ['sometimes', 'nullable', 'date'],
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
            'type.string' => 'The type must be a string.',
            'type.in' => 'The type must be one of: telcoflo, zendesk, guru, notion, confluence, website.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'last_synced_at.date' => 'The last synced at must be a valid date.',
        ];
    }
}
