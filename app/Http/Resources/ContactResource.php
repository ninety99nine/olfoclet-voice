<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'phone' => $this->phone,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'calls_count' => $this->whenCounted('calls'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links' => [
                'self' => route('show-contact', ['contact' => $this->id]),
                'update' => route('update-contact', ['contact' => $this->id]),
                'delete' => route('delete-contact', ['contact' => $this->id]),
            ],
        ];
    }
}
