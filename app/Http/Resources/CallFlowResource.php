<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallFlowResource extends JsonResource
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
            'name' => $this->name,
            'is_active' => $this->is_active,
            'organization_id' => $this->organization_id,
            'numbers' => NumberResource::collection($this->whenLoaded('numbers')),
            'nodes_count' => $this->whenCounted('nodes'),
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            '_links' => [
                'self' => route('show-call-flow', ['callFlow' => $this->id]),
                'update' => route('update-call-flow', ['callFlow' => $this->id]),
                'delete' => route('delete-call-flow', ['callFlow' => $this->id]),
            ],
        ];
    }
}
