<?php

namespace App\Http\Requests\Contact;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class DeleteContactsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Contact::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'contact_ids' => ['required', 'array', 'min:1'],
            'contact_ids.*' => ['uuid', 'exists:contacts,id'],
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
            'contact_ids.required' => 'The contact IDs are required.',
            'contact_ids.array' => 'The contact IDs must be an array.',
            'contact_ids.min' => 'At least one contact ID is required.',
            'contact_ids.*.uuid' => 'Each contact ID must be a valid UUID.',
            'contact_ids.*.exists' => 'One or more contact IDs do not exist.',
        ];
    }
}
