<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'pivot' => [
                'organization_id' => $this->pivot->organization_id ?? null,
            ],
            '_links' => [
                'self' => route('show-role', ['role' => $this->id]),
                'update' => route('update-role', ['role' => $this->id]),
                'delete' => route('delete-role', ['role' => $this->id]),
            ],
        ];
    }
}
