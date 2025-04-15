<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'type'  => $this->type,
            'name'    => $this->name,
            'email'    => $this->email,
            'updated_at' => $this->created_at->toDateString(),
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
