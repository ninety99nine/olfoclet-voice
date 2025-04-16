<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'permissions_count' => $this->whenCounted('permissions'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links'       => [
                'update_role' => route('show-role', ['role' => $this->id]),
                'delete_role' => route('show-role', ['role' => $this->id]),
            ],
        ];
    }
}
