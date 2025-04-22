<?php

namespace App\Http\Requests\Tag;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;

class DeleteTagsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', Tag::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'tag_ids' => ['required', 'array', 'min:1'],
            'tag_ids.*' => ['uuid', 'exists:tags,id'],
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
            'tag_ids.required' => 'The tag IDs are required.',
            'tag_ids.array' => 'The tag IDs must be an array.',
            'tag_ids.min' => 'At least one tag ID is required.',
            'tag_ids.*.uuid' => 'Each tag ID must be a valid UUID.',
            'tag_ids.*.exists' => 'One or more tag IDs do not exist.',
        ];
    }
}
