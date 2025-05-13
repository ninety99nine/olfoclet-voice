<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentItemResource extends JsonResource
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
            'content' => $this->content,
            'locale' => $this->locale,
            'ai_ingested' => $this->ai_ingested,
            'copilot_enabled' => $this->copilot_enabled,
            'ai_agent_enabled' => $this->ai_agent_enabled,
            'help_center_enabled' => $this->help_center_enabled,
            'visibility' => $this->visibility,
            'state' => $this->state,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'source_id' => $this->source_id,
            'help_center_collection_id' => $this->help_center_collection_id,
            'knowledge_base_id' => $this->knowledge_base_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'children_count' => $this->whenCounted('children'),
            'knowledge_base' => KnowledgeBaseResource::make($this->whenLoaded('knowledgeBase')),
            'parent' => ContentItemResource::make($this->whenLoaded('parent')),
            'source' => ContentSourceResource::make($this->whenLoaded('source')),
            'help_center_collection' => HelpCenterCollectionResource::make($this->whenLoaded('helpCenterCollection')),
            '_links' => [
                'self' => route('show-content-item', ['contentItem' => $this->id]),
                'update' => route('update-content-item', ['contentItem' => $this->id]),
                'delete' => route('delete-content-item', ['contentItem' => $this->id]),
            ],
        ];
    }
}
