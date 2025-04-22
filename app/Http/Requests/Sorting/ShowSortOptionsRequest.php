<?php

namespace App\Http\Requests\Sorting;

use App\Enums\SortResourceType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ShowSortOptionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $type = SortResourceType::tryFrom($this->input('type'));

        if (!$type) {
            return false; // Invalid or missing type
        }

        $modelClass = $this->getModelClassForType($type);
        if (!$modelClass) {
            return false; // No model mapped for this type
        }

        return $this->user()->can('viewAny', $modelClass);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(collect(SortResourceType::cases())->pluck('value')->toArray()),
            ],
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
            'type.string' => 'The type must be a string.',
            'type.in' => 'The type must be a valid resource type.',
        ];
    }

    /**
     * Map SortResourceType to model class.
     *
     * @param SortResourceType $type
     * @return string|null
     */
    private function getModelClassForType(SortResourceType $type): ?string
    {
        return match ($type) {
            SortResourceType::ROLES => \App\Models\Role::class,
            SortResourceType::USERS => \App\Models\User::class,
            SortResourceType::CALLS => \App\Models\Call::class,
            SortResourceType::QUEUES => \App\Models\Queue::class,
            SortResourceType::CONTACTS => \App\Models\Contact::class,
            SortResourceType::DEPARTMENTS => \App\Models\Department::class,
            SortResourceType::ORGANIZATIONS => \App\Models\Organization::class,
            default => null
        };
    }
}
