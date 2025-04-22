<?php

namespace App\Http\Requests\Filtering;

use App\Enums\FilterResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowFilterOptionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $type = FilterResourceType::tryFrom($this->input('type'));

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
                Rule::in(collect(FilterResourceType::cases())->pluck('value')->toArray()),
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
     * Map FilterResourceType to model class.
     *
     * @param FilterResourceType $type
     * @return string|null
     */
    private function getModelClassForType(FilterResourceType $type): ?string
    {
        return match ($type) {
            FilterResourceType::ROLES => \App\Models\Role::class,
            FilterResourceType::USERS => \App\Models\User::class,
            FilterResourceType::CALLS => \App\Models\Call::class,
            FilterResourceType::QUEUES => \App\Models\Queue::class,
            FilterResourceType::CONTACTS => \App\Models\Contact::class,
            FilterResourceType::DEPARTMENTS => \App\Models\Department::class,
            FilterResourceType::ORGANIZATIONS => \App\Models\Organization::class,
            default => null
        };
    }
}
