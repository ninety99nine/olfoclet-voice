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
            'call_flow' => $this->call_flow,
            'is_active' => $this->is_active,
            'first_step' => $this->first_step,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links' => [
                'self' => route('show-number', ['number' => $this->id]),
                'update' => route('update-number', ['number' => $this->id]),
                'delete' => route('delete-number', ['number' => $this->id]),
            ],
        ];
    }
}
