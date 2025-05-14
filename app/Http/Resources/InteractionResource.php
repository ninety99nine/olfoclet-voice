<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InteractionResource extends JsonResource
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
            'call_id' => $this->call_id,
            'omni_channel_message_id' => $this->omni_channel_message_id,
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'call' => CallResource::make($this->whenLoaded('call')),
            'omni_channel_message' => OmniChannelMessageResource::make($this->whenLoaded('omniChannelMessage')),
            '_links' => [
                'self' => route('show-interaction', ['interaction' => $this->id]),
                'update' => route('update-interaction', ['interaction' => $this->id]),
                'delete' => route('delete-interaction', ['interaction' => $this->id]),
            ],
        ];
    }
}
