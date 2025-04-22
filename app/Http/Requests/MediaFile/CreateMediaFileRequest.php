<?php

namespace App\Http\Requests\MediaFile;

use App\Models\MediaFile;
use Illuminate\Foundation\Http\FormRequest;

class CreateMediaFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', MediaFile::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'organization_id' => ['required', 'uuid', 'exists:organizations,id'],
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
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'file.required' => 'The file is required.',
            'file.file' => 'The file must be a valid file.',
            'file.max' => 'The file must not exceed 10MB.',
            'organization_id.required' => 'The organization ID is required.',
            'organization_id.uuid' => 'The organization ID must be a valid UUID.',
            'organization_id.exists' => 'The specified organization does not exist.',
        ];
    }
}
