<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'seats' => $this->seats,
            'alias' => $this->alias,
            'active' => $this->active,
            'country' => $this->country,
            'users_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            '_links' => [
                'self' => route('show-organization', ['organization' => $this->id]),
                'update' => route('update-organization', ['organization' => $this->id]),
                'delete' => route('delete-organization', ['organization' => $this->id]),
            ],
        ];
    }
}
