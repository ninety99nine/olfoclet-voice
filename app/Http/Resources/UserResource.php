<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'type' => $this->type,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'organizations_count' => $this->whenCounted('organizations'),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'organizations' => OrganizationResource::collection($this->whenLoaded('organizations')),
            '_links' => [
                'self' => route('show-user', ['user' => $this->id]),
                'update' => route('update-user', ['user' => $this->id]),
                'delete' => route('delete-user', ['user' => $this->id]),
            ],
        ];
    }
}
