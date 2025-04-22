<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallFlowNodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'call_flow_id' => $this->call_flow_id,
            'type' => $this->type,
            'next_step' => $this->next_step,
            'metadata' => $this->metadata,
            'position' => $this->position,
            'media_files' => MediaFileResource::collection($this->whenLoaded('mediaFiles')),
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            '_links' => [
                'self' => route('show-call-flow-node', ['callFlowNode' => $this->id]),
                'update' => route('update-call-flow-node', ['callFlowNode' => $this->id]),
                'delete' => route('delete-call-flow-node', ['callFlowNode' => $this->id]),
            ],
        ];
    }
}
