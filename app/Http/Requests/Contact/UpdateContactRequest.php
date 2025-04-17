<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->contact);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:contacts,email,' . $this->contact->id,
            'phone' => 'sometimes|string|max:20',
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
            'name.max' => 'The name must not exceed 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'The email address is already in use.',
            'email.max' => 'The email must not exceed 255 characters.',
            'phone.max' => 'The phone number must not exceed 20 characters.',
        ];
    }
}
