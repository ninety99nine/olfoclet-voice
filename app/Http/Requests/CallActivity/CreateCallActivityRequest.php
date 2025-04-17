<?php

namespace App\Http\Requests\Contact;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class CreateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Contact::class);
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
            'email' => 'required|email|max:255|unique:contacts,email',
            'phone' => 'required|string|max:20',
            'organization_id' => 'required|uuid|exists:organizations,id',
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
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'The email address is already in use.',
            'email.max' => 'The email must not exceed 255 characters.',
            'phone.required' => 'The phone number is required.',
            'phone.max' => 'The phone number must not exceed 20 characters.',
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.exists' => 'The specified organization does not exist.',
        ];
    }
}
