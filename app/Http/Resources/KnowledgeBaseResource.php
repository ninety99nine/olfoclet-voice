<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KnowledgeBaseResource extends JsonResource
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
            'organization_id' => $this->organization_id,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
            'articles_count' => $this->whenCounted('articles'),
            'snippets_count' => $this->whenCounted('snippets'),
            'websites_count' => $this->whenCounted('websites'),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
            '_links' => [
                'self' => route('show-knowledge-base', ['knowledgeBase' => $this->id]),
                'update' => route('update-knowledge-base', ['knowledgeBase' => $this->id]),
                'delete' => route('delete-knowledge-base', ['knowledgeBase' => $this->id]),
            ],
        ];
    }
}
