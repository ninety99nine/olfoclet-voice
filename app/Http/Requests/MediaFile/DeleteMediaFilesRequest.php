<?php

namespace App\Http\Requests\MediaFile;

use App\Models\MediaFile;
use Illuminate\Foundation\Http\FormRequest;

class DeleteMediaFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', MediaFile::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'media_file_ids' => ['required', 'array', 'min:1'],
            'media_file_ids.*' => ['uuid', 'exists:media_files,id'],
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
            'media_file_ids.required' => 'The media file IDs are required.',
            'media_file_ids.array' => 'The media file IDs must be an array.',
            'media_file_ids.min' => 'At least one media file ID is required.',
            'media_file_ids.*.uuid' => 'Each media file ID must be a valid UUID.',
            'media_file_ids.*.exists' => 'One or more media file IDs do not exist.',
        ];
    }
}
