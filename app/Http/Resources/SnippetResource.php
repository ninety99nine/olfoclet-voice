<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SnippetResource extends JsonResource
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
            'ai_searchable' => $this->ai_searchable,
            'organization_id' => $this->organization_id,
            'knowledge_base_id' => $this->knowledge_base_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            'knowledge_base' => KnowledgeBaseResource::make($this->whenLoaded('knowledgeBase')),
            '_links' => [
                'self' => route('show-snippet', ['snippet' => $this->id]),
                'update' => route('update-snippet', ['snippet' => $this->id]),
                'delete' => route('delete-snippet', ['snippet' => $this->id]),
            ],
        ];
    }
}
