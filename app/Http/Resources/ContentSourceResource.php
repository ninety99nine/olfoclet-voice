<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentSourceResource extends JsonResource
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
            'type' => $this->type,
            'name' => $this->name,
            'last_synced_at' => $this->last_synced_at ? $this->last_synced_at->toDateTimeString() : null,
            'knowledge_base_id' => $this->knowledge_base_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'content_items_count' => $this->whenCounted('contentItems'),
            'knowledge_base' => KnowledgeBaseResource::make($this->whenLoaded('knowledgeBase')),
            '_links' => [
                'self' => route('show-content-source', ['contentSource' => $this->id]),
                'update' => route('update-content-source', ['contentSource' => $this->id]),
                'delete' => route('delete-content-source', ['contentSource' => $this->id]),
            ],
        ];
    }
}
