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
            'primary_phone' => $this->primary_phone,
            'primary_email' => $this->primary_email,
            'organization_id' => $this->organization_id,
            'calls_count' => $this->whenCounted('calls'),
            'favorite_user_id' => $this->favorite_user_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'favorite_user' => UserResource::make($this->whenLoaded('favoriteUser')),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'identifiers' => ContactIdentifierResource::collection($this->whenLoaded('identifiers')),
            'custom_attributes' => ContactCustomAttributeResource::collection($this->whenLoaded('customAttributes')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            '_links' => [
                'self' => route('show-contact', ['contact' => $this->id]),
                'update' => route('update-contact', ['contact' => $this->id]),
                'delete' => route('delete-contact', ['contact' => $this->id]),
            ],
        ];
    }
}
