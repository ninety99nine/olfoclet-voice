<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'email' => $this->email,
            'updated_at' => $this->created_at->toDateString(),
            'created_at' => $this->created_at->toDateString(),
            'organizations_count' => $this->whenCounted('organizations'),
            '_links' => [
                'update_user' => route('show-user', ['user' => $this->resource->id]),
                'delete_user' => route('show-user', ['user' => $this->resource->id])
            ]
        ];
    }
}
