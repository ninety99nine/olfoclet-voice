<?php

namespace App\Http\Requests\Website;

use App\Models\Website;
use Illuminate\Foundation\Http\FormRequest;

class DeleteWebsitesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Website::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'website_ids' => ['required', 'array', 'min:1'],
            'website_ids.*' => ['uuid', 'exists:websites,id'],
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
            'website_ids.required' => 'The website IDs are required.',
            'website_ids.array' => 'The website IDs must be an array.',
            'website_ids.min' => 'At least one website ID is required.',
            'website_ids.*.uuid' => 'Each website ID must be a valid UUID.',
            'website_ids.*.exists' => 'One or more website IDs do not exist.',
        ];
    }
}
