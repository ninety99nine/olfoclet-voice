<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationThreadResource extends JsonResource
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
            'title' => $this->title,
            'user_id' => $this->user_id,
            'copilot_id' => $this->copilot_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'messages_count' => $this->whenCounted('messages'),
            'user' => UserResource::make($this->whenLoaded('user')),
            'copilot' => CopilotResource::make($this->whenLoaded('copilot')),
            '_links' => [
                'self' => route('show-conversation-thread', ['conversationThread' => $this->id]),
                'update' => route('update-conversation-thread', ['conversationThread' => $this->id]),
                'delete' => route('delete-conversation-thread', ['conversationThread' => $this->id]),
            ],
        ];
    }
}
