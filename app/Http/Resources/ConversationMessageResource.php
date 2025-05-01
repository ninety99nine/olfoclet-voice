<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationMessageResource extends JsonResource
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
            'role' => $this->role,
            'content' => $this->content,
            'context' => $this->context,
            'thread_id' => $this->thread_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'thread' => ConversationThreadResource::make($this->whenLoaded('thread')),
            '_links' => [
                'self' => route('show-conversation-message', ['conversationMessage' => $this->id]),
                'update' => route('update-conversation-message', ['conversationMessage' => $this->id]),
                'delete' => route('delete-conversation-message', ['conversationMessage' => $this->id]),
            ],
        ];
    }
}
