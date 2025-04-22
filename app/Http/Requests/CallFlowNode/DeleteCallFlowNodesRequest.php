<?php

namespace App\Http\Requests\CallFlowNode;

use App\Models\CallFlowNode;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCallFlowNodesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('deleteAny', CallFlowNode::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'call_flow_node_ids' => ['required', 'array', 'min:1'],
            'call_flow_node_ids.*' => ['uuid', 'exists:call_flow_nodes,id'],
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
            'call_flow_node_ids.required' => 'The call flow node IDs are required.',
            'call_flow_node_ids.array' => 'The call flow node IDs must be an array.',
            'call_flow_node_ids.min' => 'At least one call flow node ID is required.',
            'call_flow_node_ids.*.uuid' => 'Each call flow node ID must be a valid UUID.',
            'call_flow_node_ids.*.exists' => 'One or more call flow node IDs do not exist.',
        ];
    }
}
