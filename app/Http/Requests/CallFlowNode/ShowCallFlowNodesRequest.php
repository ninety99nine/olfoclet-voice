<?php

namespace App\Http\Requests\CallFlowNode;

use App\Models\CallFlowNode;
use Illuminate\Foundation\Http\FormRequest;

class ShowCallFlowNodesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', CallFlowNode::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_flow_id' => ['sometimes', 'uuid', 'exists:call_flows,id'],
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
            'call_flow_id.uuid' => 'The call flow ID must be a valid UUID.',
            'call_flow_id.exists' => 'The specified call flow does not exist.',
        ];
    }
}
