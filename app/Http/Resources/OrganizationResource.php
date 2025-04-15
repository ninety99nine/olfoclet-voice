<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'seats' => $this->seats,
            'alias' => $this->alias,
            'active' => $this->active,
            'country' => $this->country,
            'users_count' => $this->whenCounted('users'),
            'updated_at' => $this->created_at->toDateString(),
            'created_at' => $this->created_at->toDateString(),
            '_links' => [
                'update_organization' => route('show-organization', ['organization' => $this->resource->id]),
                'delete_organization' => route('show-organization', ['organization' => $this->resource->id])
            ]
        ];
    }
}
