<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NumberResource extends JsonResource
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
            'number' => $this->number,
            'provider' => $this->provider,
            'is_active' => $this->is_active,
            'call_flow_id' => $this->call_flow_id,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'call_flow' => CallFlowResource::make($this->whenLoaded('callFlow')),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links' => [
                'self' => route('show-number', ['number' => $this->id]),
                'update' => route('update-number', ['number' => $this->id]),
                'delete' => route('delete-number', ['number' => $this->id]),
            ],
        ];
    }
}
