<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'alias'    => $this->alias,
            'active'  => $this->active,
            'country' => $this->country,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
