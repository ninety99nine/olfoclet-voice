<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OmniChannelMessageResource extends JsonResource
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
            'channel' => $this->channel,
            'direction' => $this->direction,
            'status' => $this->status,
            'from' => $this->from,
            'to' => $this->to,
            'content' => $this->content,
            'message_type' => $this->message_type,
            'external_message_id' => $this->external_message_id,
            'organization_id' => $this->organization_id,
            'contact_id' => $this->contact_id,
            'agent_id' => $this->agent_id,
            'queue_id' => $this->queue_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'contact' => ContactResource::make($this->whenLoaded('contact')),
            'agent' => UserResource::make($this->whenLoaded('agent')),
            'queue' => QueueResource::make($this->whenLoaded('queue')),
            '_links' => [
                'self' => route('show-omni-channel-message', ['omniChannelMessage' => $this->id]),
                'update' => route('update-omni-channel-message', ['omniChannelMessage' => $this->id]),
                'delete' => route('delete-omni-channel-message', ['omniChannelMessage' => $this->id]),
            ],
        ];
    }
}
