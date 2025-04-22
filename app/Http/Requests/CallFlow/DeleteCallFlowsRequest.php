<?php

namespace App\Http\Requests\CallFlow;

use App\Models\CallFlow;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCallFlowsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', CallFlow::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_flow_ids' => ['required', 'array', 'min:1'],
            'call_flow_ids.*' => ['uuid', 'exists:call_flows,id'],
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
            'call_flow_ids.required' => 'The call flow IDs are required.',
            'call_flow_ids.array' => 'The call flow IDs must be an array.',
            'call_flow_ids.min' => 'At least one call flow ID is required.',
            'call_flow_ids.*.uuid' => 'Each call flow ID must be a valid UUID.',
            'call_flow_ids.*.exists' => 'One or more call flow IDs do not exist.',
        ];
    }
}
